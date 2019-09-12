<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductWasRenamed extends AggregateChanged
{
    public static function record(string $aggregateId, string $listId, string $newName)
    {
        return new static($aggregateId, ['list_id' => $listId, 'new_name' => $newName]);
    }

    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function name(): string
    {
        return $this->payload['new_name'];
    }
}
