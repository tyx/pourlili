<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\SharedKernel\Bridge\CommandBus;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UploadImageWhenProductWasRegistered implements MessageSubscriberInterface
{
    private $commandBus;

    private $httpClient;

    public function __construct(CommandBus $commandBus, HttpClientInterface $httpClient)
    {
        $this->commandBus = $commandBus;
        $this->httpClient = $httpClient;
    }


    public function uploadImage(ProductWasRegistered $event)
    {
       /* $response = $this->httpClient->request('GET', $event->imagePath(), [
            'buffer' => false,
        ]);*/
    }

    public static function getHandledMessages(): iterable
    {
        yield ProductWasRegistered::class => [
            'method' => 'uploadImage',
            'bus' => 'event.bus',
        ];
    }
}
