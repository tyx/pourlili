<?php
namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class MoneyWasCollected extends AggregateChanged
{
    public function productId()
    {
        return $this->payload['product_id'];
    }

    public function amount()
    {
        return $this->payload['amount'];
    }
}