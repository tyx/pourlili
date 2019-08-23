<?php
namespace App\Contribution\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ContributionWasCheckedOut extends AggregateChanged
{
    public function amount()
    {
        return $this->payload['amount'];
    }

    public function author()
    {
        return $this->payload['author'];
    }

    public function basketId()
    {
        return $this->payload['basket_id'];
    }

    public function listId()
    {
        return $this->payload['list_id'];
    }
}