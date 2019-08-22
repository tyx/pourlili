<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductListWasStarted extends AggregateChanged
{
    public static function record(string $aggregateId, string $name): self
    {
        return new static($aggregateId, ['name' => $name]);
    }

    public function name(): string
    {
        return $this->payload['name'];
    }
}
