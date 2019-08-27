<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductPriceWasChanged extends AggregateChanged
{
    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function price(): float
    {
        return (float) $this->payload['new_price'];
    }
}
