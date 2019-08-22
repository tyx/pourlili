<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductListWasSorted extends AggregateChanged
{
    public function sort()
    {
        return $this->payload['sort'];
    }
}
