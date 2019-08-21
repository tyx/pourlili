<?php
declare(strict_types=1);

namespace App\Catalog\Infra;

use App\Catalog\Domain\ImageUploader;
use App\Catalog\Domain\ThumbnailGenerator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class S3ImageUploader implements ImageUploader
{
    private $bucket;

    private $region;

    private $httpClient;

    private $thumbnailGenerator;

    public function __construct(
        string $bucket,
        string $region,
        HttpClientInterface $httpClient,
        ThumbnailGenerator $thumbnailGenerator
    ) {
        $this->bucket = $bucket;
        $this->region = $region;
        $this->httpClient = $httpClient;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    public function upload(string $path, string $name, int $maxHeight)
    {
        $response = $this->httpClient->request('GET', $path, [
            'buffer' => false,
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \LogicException("Cannot download image {$path}");
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $temp = tmpfile();
        $tempPath = stream_get_meta_data($temp)['uri'];
        foreach ($this->httpClient->stream($response) as $chunk) {
            fwrite($temp, $chunk->getContent());
        }
        $this->thumbnailGenerator->createThumbnail(
            $tempPath,
            "s3://{$this->bucket}/{$name}.{$extension}",
            $maxHeight
        );

        fclose($temp);

        return "{$this->bucket}.s3.{$this->region}.amazonaws.com/{$name}.{$extension}";
    }
}
