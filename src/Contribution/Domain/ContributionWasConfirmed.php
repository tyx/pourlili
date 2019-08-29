<?php
namespace App\Contribution\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ContributionWasConfirmed extends AggregateChanged
{
    public function basketId()
    {
        return $this->payload['basket_id'];
    }

    public function listId()
    {
        return $this->payload['list_id'];
    }

    public function amount()
    {
        return $this->payload['amount'];
    }
}
