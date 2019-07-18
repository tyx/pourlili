<?php
namespace App\Catalog\App\Command;

use Ramsey\Uuid\Uuid;

class CollectMoneyForProduct
{
    private $listId;

    private $productId;

    private $amount;

    public function __construct(Uuid $listId, Uuid $productId, float $amount)
    {
        $this->listId = $listId;
        $this->productId = $productId;
        $this->amount = $amount;
    }

    public function listId(): Uuid
    {
        return $this->listId;
    }

    public function productId(): Uuid
    {
        return $this->productId;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}