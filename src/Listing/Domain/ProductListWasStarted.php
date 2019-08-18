<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductListWasStarted extends AggregateChanged
{
    public static function record(string $aggregateId, string $host): self
    {
        return new static($aggregateId, ['host' => $host]);
    }

    public function host(): string
    {
        return $this->payload['host'];
    }
}
