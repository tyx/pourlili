<?php
namespace App\Basket\Domain;

use Prooph\EventSourcing\AggregateChanged;

class BasketWasPickedUp extends AggregateChanged
{
    public function listId()
    {
        return $this->payload['list_id'];
    }
}