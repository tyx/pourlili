<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductListWasStarted extends AggregateChanged
{
    public static function record(string $aggregateId): self
    {
        return new static($aggregateId, []);
    }
}
