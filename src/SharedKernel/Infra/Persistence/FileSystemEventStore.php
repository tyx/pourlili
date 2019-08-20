<?php
declare(strict_types=1);

namespace App\SharedKernel\Infra\Persistence;

use Aws\S3\S3Client;
use Iterator;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\Common\Messaging\MessageDataAssertion;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use SplFileObject;

class FileSystemEventStore implements EventStore
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var MessageConverter
     */
    private $messageConverter;

    /**
     * @param string           $rootDir          The root directory to store the event files
     * @param MessageFactory   $messageFactory   To create message from array
     * @param MessageConverter $messageConverter To convert message into array
     */
    public function __construct($rootDir, MessageFactory $messageFactory, MessageConverter $messageConverter, S3Client $s3)
    {
        $s3->registerStreamWrapper();
        $this->rootDir = $rootDir;
        $this->messageFactory = $messageFactory;
        $this->messageConverter = $messageConverter;
    }

    /**
     * @param Stream $stream
     */
    public function create(Stream $stream): void
    {
        $this->appendTo($stream->streamName(), $stream->streamEvents());
    }

    /**
     * @param StreamName $streamName
     * @param Iterator   $domainEvents
     */
    public function appendTo(StreamName $streamName, Iterator $streamEvents): void
    {
        $path = $this->buildPath($streamName->toString());
        $dir = dirname($path);

        if (false === is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $contents = '';

        foreach ($streamEvents as $change) {
            $contents .= $this->convertEventToJson($change).PHP_EOL;
        }

        $fp = fopen($path, 'a');

        $result = fwrite($fp, $contents);
        fclose($fp);

        if ($result === false) {
            throw new \RuntimeException('Error during event store writing');
        }
    }

    public function load(
        StreamName $streamName,
        int $fromNumber = 1,
        int $count = null,
        MetadataMatcher $metadataMatcher = null
    ): Iterator {
        try {
            $file = new SplFileObject($this->buildPath($streamName->toString()));
        } catch (\Exception $e) {
            return null;
        }

        $file->setFlags(SplFileObject::SKIP_EMPTY | SplFileObject::READ_AHEAD);

        while (!$file->eof()) {
            $data = json_decode($file->current(), true);
            $data['created_at'] = \DateTimeImmutable::createFromFormat(
                'Y-m-d\TH:i:s.u',
                $data['created_at'],
                new \DateTimeZone('UTC')
            );
            yield $this->messageFactory->createMessageFromArray(
                $data['message_name'],
                $data
            );
            $file->next();
        }
    }

    public function updateStreamMetadata(StreamName $streamName, array $newMetadata): void
    {
        throw new \RuntimeException(__METHOD__);
    }

    public function delete(StreamName $streamName): void
    {
        throw new \RuntimeException('need impl');
    }

    public function fetchStreamMetadata(StreamName $streamName): array
    {
        throw new \RuntimeException(__METHOD__);
    }

    public function hasStream(StreamName $streamName): bool
    {
        throw new \RuntimeException(__METHOD__);
    }

    public function loadReverse(
        StreamName $streamName,
        int $fromNumber = null,
        int $count = null,
        MetadataMatcher $metadataMatcher = null
    ): Iterator {
        throw new \RuntimeException(__METHOD__);
    }

    /**
     * @return StreamName[]
     */
    public function fetchStreamNames(
        ?string $filter,
        ?MetadataMatcher $metadataMatcher,
        int $limit = 20,
        int $offset = 0
    ): array {
        throw new \RuntimeException(__METHOD__);
    }

    /**
     * @return StreamName[]
     */
    public function fetchStreamNamesRegex(
        string $filter,
        ?MetadataMatcher $metadataMatcher,
        int $limit = 20,
        int $offset = 0
    ): array {
        throw new \RuntimeException(__METHOD__);
    }

    /**
     * @return string[]
     */
    public function fetchCategoryNames(?string $filter, int $limit = 20, int $offset = 0): array
    {
        throw new \RuntimeException(__METHOD__);
    }

    /**
     * @return string[]
     */
    public function fetchCategoryNamesRegex(string $filter, int $limit = 20, int $offset = 0): array
    {
        throw new \RuntimeException(__METHOD__);
    }

    private function convertEventToJson(Message $event): string
    {
        $eventArr = $this->messageConverter->convertToArray($event);

        MessageDataAssertion::assert($eventArr);

        $eventArr['created_at'] = $eventArr['created_at']->format('Y-m-d\TH:i:s.u');

        return json_encode($eventArr);
    }

    private function buildPath(string $streamName): string
    {
        $prefix = strstr($streamName, '-', true);
        $streamNameHashed = sha1($streamName);

        return
            "{$this->rootDir}/{$prefix}/".
            substr($streamNameHashed, 0, 2).'/'.
            substr($streamNameHashed, 2, 2).'/'.
            $streamName
        ;
    }
}
