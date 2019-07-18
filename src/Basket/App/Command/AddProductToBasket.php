<?php

namespace App\Basket\App\Command;

use Ramsey\Uuid\Uuid;

class AddProductToBasket
{
    private $basketId;

    private $productId;

    private $amountOfContribution;

    public function __construct(Uuid $basketId, string $productId, float $amountOfContribution)
    {
        $this->basketId = $basketId;
        $this->productId = $productId;
        $this->amountOfContribution = $amountOfContribution;
    }

    public function basketId()
    {
        return $this->basketId;
    }

    public function productId()
    {
        return $this->productId;
    }

    public function amountOfContribution()
    {
        return $this->amountOfContribution;
    }
}