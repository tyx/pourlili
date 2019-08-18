<?php
namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class MoneyWasCollected extends AggregateChanged
{
    public function listId()
    {
        return $this->payload['list_id'];
    }

    public function amount()
    {
        return $this->payload['amount'];
    }
}