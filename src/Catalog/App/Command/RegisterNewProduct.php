<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use Ramsey\Uuid\UuidInterface;

class RegisterNewProduct
{
    private $listId;

    private $id;

    private $name;

    private $price;

    private $imagePath;

    private $description;

    private $uploadedPath;

    public function __construct(UuidInterface $listId, UuidInterface $id, string $name, ?float $price, ?string $imagePath, string $description, ?string $uploadedPath = null)
    {
        $this->listId = $listId;
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->imagePath = $imagePath;
        $this->description = $description;
        $this->uploadedPath = $uploadedPath;
    }

    public function listId(): UuidInterface
    {
        return $this->listId;
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

    public function uploadedPath(): ?string
    {
        return $this->uploadedPath;
    }
}
