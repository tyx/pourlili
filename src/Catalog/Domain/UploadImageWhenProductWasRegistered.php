<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\Catalog\App\Command\UploadImageOfProduct;
use App\SharedKernel\Bridge\CommandBus;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class UploadImageWhenProductWasRegistered implements MessageSubscriberInterface
{
    private $imageUploader;

    private $logger;

    private $commandBus;

    public function __construct(ImageUploader $imageUploader, LoggerInterface $logger, CommandBus $commandBus)
    {
        $this->imageUploader = $imageUploader;
        $this->logger = $logger;
        $this->commandBus = $commandBus;
    }

    public function uploadImage(ProductWasRegistered $event)
    {
        $image = $this->imageUploader->upload($event->imagePath(), $event->aggregateId(), 300);

        if (false === $image) {
            $this->logger->error("Cannot upload image : {$event->imagePath()}");

            return;
        }

        $this->commandBus->execute(new UploadImageOfProduct(
            Uuid::fromString($event->aggregateId()),
            $image
        ));
    }

    public static function getHandledMessages(): iterable
    {
        yield ProductWasRegistered::class => [
            'method' => 'uploadImage',
            'bus' => 'event.bus',
        ];
    }
}
