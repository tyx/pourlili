<?php
declare(strict_types=1);

namespace App\SharedKernel\Bridge;

use Aws\S3\S3Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class S3StreamWrapperListener implements EventSubscriberInterface
{
    private $s3;

    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['registerStreamWrapper', 19],
            ],
        ];
    }

    public function registerStreamWrapper(RequestEvent $event)
    {
        $this->s3->registerStreamWrapper();
    }
}
