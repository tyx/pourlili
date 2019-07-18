<?php
namespace App\Basket\App\Query;

class ShowBasket
{
    private $basketId;

    public function __construct($basketId)
    {
        $this->basketId = $basketId;
    }

    public function basketId()
    {
        return $this->basketId;
    }
}