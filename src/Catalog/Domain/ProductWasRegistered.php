<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductWasRegistered extends AggregateChanged
{
    public static function record(string $aggregateId, string $name, float $price, string $description): self
    {
        return new static($aggregateId, ['name' => $name, 'price' => $price, 'description' => $description]);
    }

    public function name(): string
    {
        return $this->payload['name'];
    }

    public function price(): float
    {
        return $this->payload['price'];
    }

    public function description(): string
    {
        return $this->payload['description'];
    }
}
