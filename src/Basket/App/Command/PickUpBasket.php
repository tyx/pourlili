<?php

namespace App\Basket\App\Command;

use Ramsey\Uuid\Uuid;

class PickUpBasket
{
    private $listId;

    private $basketId;

    public function __construct(Uuid $basketId, string $listId)
    {
        $this->basketId = $basketId;
        $this->listId = $listId;
    }

    public function basketId(): Uuid
    {
        return $this->basketId;
    }

    public function listId(): string
    {
        return $this->listId;
    }
}