<?php
declare(strict_types=1);

namespace App\SharedKernel\Bridge;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function query(object $query)
    {
        return $this->handle($query);
    }
}
