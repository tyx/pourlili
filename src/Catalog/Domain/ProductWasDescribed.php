<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductWasDescribed extends AggregateChanged
{
    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function description(): string
    {
        return $this->payload['new_description'];
    }
}
