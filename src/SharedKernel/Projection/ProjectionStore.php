<?php
declare(strict_types=1);

namespace App\SharedKernel\Projection;

use Prooph\EventSourcing\AggregateChanged;
use App\SharedKernel\Projection\Projection;

class ProjectionStore
{
    private $rootDir;

    private $uow = [];

    private $pending = [];

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function updateProjection(string $projectionName, AggregateChanged $event, callable $updator, ?string $aggregateId = null)
    {
        $projection = $this->load($aggregateId ?? $event->aggregateId(), $projectionName);
        $projection->update($event, $updator);
        $this->save($projection);
    }

    public function load(string $name, ?string $id = null)
    {
        $projection = new Projection($name, $id);

        if (array_key_exists($projection->id(), $this->uow)) {
            return $this->uow[$projection->id()];
        }

        $projection = $this->loadState($projection);
        $this->uow[$projection->id()] = $projection;

        return $projection;
    }

    public function commit(Projection $projection)
    {
        $this->uow[$projection->id()] = $projection;
        $this->pending[$projection->id()] = $projection; // Could be replaced by a dedicated object in uow with a modified bool attribute
    }

    public function flush()
    {
        foreach ($this->pending as $projection) {
            $this->save($projection);
        }
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

    private function loadState(Projection $projection)
    {
        $filename = $this->buildPath($projection);

        if (false === file_exists($filename)) {
            return $projection;
        }

        return $projection->withState(
            json_decode(file_get_contents($filename), true)
        );
    }

    private function buildPath(Projection $projection): string
    {
        $streamNameHashed = $projection->hash();

        return
            "{$this->rootDir}/{$projection->name()}/".
            substr($streamNameHashed, 0, 2).'/'.
            substr($streamNameHashed, 2, 2).'/'.
            "{$projection->id()}.json"
        ;
    }
}
