<?php
namespace App\Catalog\App\Command;

use Ramsey\Uuid\Uuid;

class CollectMoneyForProduct
{
    private $productId;

    private $amount;

    public function __construct(Uuid $productId, float $amount)
    {
        $this->productId = $productId;
        $this->amount = $amount;
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