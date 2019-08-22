<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Prooph\EventSourcing\AggregateChanged;

class HostWasRegisteredOnList extends AggregateChanged
{
    public function hostName(): string
    {
        return $this->payload['host_name'];
    }

    public function hostEnabled(): bool
    {
        return $this->payload['host_enabled'];
    }
}
