<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductWasRegistered extends AggregateChanged
{
    public static function record(string $aggregateId, string $listId): self
    {
        return new static($aggregateId, ['list_id' => $listId]);
    }

    public function listId(): string
    {
        return $this->payload['list_id'];
    }
}
