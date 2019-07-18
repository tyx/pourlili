<?php
namespace App\Basket\Domain;

class Product
{
    private $productId;

    private $amountOfContribution;

    public function __construct($productId, $amountOfContribution)
    {
        $this->productId = $productId;
        $this->amountOfContribution = $amountOfContribution;
    }
}