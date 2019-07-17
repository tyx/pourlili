<?php
declare(strict_types=1);

namespace App\Catalog\App;

class RegisterNewProduct
{
    private $name;

    private $price;

    private $imagePath;

    private $description;

    public function __construct(string $name, float $price, string $imagePath, string $description)
    {
        $this->name = $name;
        $this->price = $price;
        $this->imagePath = $imagePath;
        $this->description = $description;
    }
}
