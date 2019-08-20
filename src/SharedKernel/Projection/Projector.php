<?php
declare(strict_types=1);

namespace App\SharedKernel\Projection;

use Prooph\EventSourcing\AggregateChanged;
use App\SharedKernel\Projection\Projection;
use Aws\S3\S3Client;

class Projector
{
    private $rootDir;

    public function __construct(string $rootDir, S3Client $s3)
    {
        $this->rootDir = $rootDir;
        $s3->registerStreamWrapper();
    }

    public function updateProjection(string $projectionName, AggregateChanged $event, callable $updator, ?string $aggregateId = null)
    {
        $projection = $this->load($aggregateId ?? $event->aggregateId(), $projectionName);
        $projection->update($event, $updator);
        $this->save($projection);
    }

    public function load(string $aggregateId, string $name)
    {
        $projection = new Projection($aggregateId, $name);
        $filename = $this->buildPath($projection);

        if (false === file_exists($filename)) {
            return $projection;
        }

        $projection->loadState(json_decode(file_get_contents($filename), true));

        return $projection;
    }

    public function save(Projection $projection)
    {
        $path = $this->buildPath($projection);
        $dir = dirname($path);

        if (false === is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen($path, 'w');
        $result = fwrite($fp, json_encode($projection->state()));
        fclose($fp);

        if ($result === false) {
            throw new \RuntimeException('Error during event store writing');
        }
    }

    private function buildPath(Projection $projection): string
    {
        $streamNameHashed = sha1($projection->aggregateId());

        return
            "{$this->rootDir}/{$projection->name()}/".
            substr($streamNameHashed, 0, 2).'/'.
            substr($streamNameHashed, 2, 2).'/'.
            $projection->aggregateId().'.json'
        ;
    }
}
