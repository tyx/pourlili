<?php
namespace App\Contribution\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ContributionWasSigned extends AggregateChanged
{
    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function signature(): string
    {
        return $this->payload['signature'];
    }

    public function message(): string
    {
        return $this->payload['message'];
    }
}
