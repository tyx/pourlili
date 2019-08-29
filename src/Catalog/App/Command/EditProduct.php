<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use Ramsey\Uuid\UuidInterface;

class EditProduct
{
    private $id;

    private $name;

    private $price;

    private $imagePath;

    private $description;

    public function __construct(UuidInterface $id, string $name, ?float $price, ?string $imagePath, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->imagePath = $imagePath;
        $this->description = $description;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): ?float
    {
        return $this->price;
    }

    public function image(): ?string
    {
        return $this->imagePath;
    }

    public function description(): string
    {
        return $this->description;
    }
}
