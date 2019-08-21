<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ImageOfProductWasUploaded extends AggregateChanged
{
    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function path(): string
    {
        return $this->payload['path'];
    }
}
