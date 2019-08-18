<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductWasRegistered extends AggregateChanged
{
    public static function record(string $aggregateId, string $listId, string $name, float $price, string $description): self
    {
        return new static($aggregateId, ['list_id' => $listId, 'name' => $name, 'price' => $price, 'description' => $description]);
    }

    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function name(): string
    {
        return $this->payload['name'];
    }

    public function price(): float
    {
        return (float) $this->payload['price'];
    }

    public function description(): string
    {
        return $this->payload['description'];
    }
}
