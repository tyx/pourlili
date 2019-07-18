<?php
declare(strict_types=1);

namespace App\SharedKernel\Bridge;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function execute(object $command): void
    {
        $this->handle($command);
    }
}
