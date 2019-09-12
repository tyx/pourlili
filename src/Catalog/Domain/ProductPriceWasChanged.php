<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductPriceWasChanged extends AggregateChanged
{
    public static function record(string $aggregateId, string $listId, float $newPrice)
    {
        return new static($aggregateId, ['list_id' => $listId, 'new_price' => $newPrice]);
    }

    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function price(): float
    {
        return (float) $this->payload['new_price'];
    }
}
