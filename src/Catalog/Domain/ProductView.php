<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

class ProductView
{
    private $id;

    private $name;

    private $price;

    private $description;

    public function __construct($id, $name, $price, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
    }

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }

    public function price()
    {
        return $this->price;
    }

    public function description()
    {
        return $this->description;
    }

    public function image()
    {
        return '';
    }

    public function alreadyCollected()
    {
        return 0;
    }

    public function remainingAmountToCollect()
    {
        return 0;
    }

    public function isFunded()
    {
        return false;
    }
}
