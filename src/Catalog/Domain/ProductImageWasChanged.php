<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductImageWasChanged extends AggregateChanged
{
    public function listId(): string
    {
        return $this->payload['list_id'];
    }

    public function imagePath(): string
    {
        return $this->payload['new_original_image'];
    }
}
