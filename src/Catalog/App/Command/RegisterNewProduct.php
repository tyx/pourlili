<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

class RegisterNewProduct
{
    private $listId;

    private $name;

    private $price;

    private $imagePath;

    private $description;

    public function __construct(string $listId, string $name, float $price, string $imagePath, string $description)
    {
        $this->name = $name;
        $this->price = $price;
        $this->imagePath = $imagePath;
        $this->description = $description;
    }
}
