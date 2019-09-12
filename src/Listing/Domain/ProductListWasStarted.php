<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductListWasStarted extends AggregateChanged
{
    public static function record(string $aggregateId, string $host, ?string $originId = null): self
    {
        return new static($aggregateId, ['host' => $host, 'origin_id' => $originId]);
    }

    public function host(): string
    {
        return $this->payload['host'];
    }

    public function originId(): ?string
    {
        return $this->payload['origin_id'];
    }
}
