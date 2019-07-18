<?php

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;

final class Product 
{
    private $id;

    private $listId;

    private $name;

    private $price;

    private $imagePath;

    private $description;

    private $alreadyCollected = 0;

    public static function register(Uuid $listId, Uuid $id, string $name, float $price, string $imagePath, string $description)
    {
        $product = new self;
        $product->id = $id;
        $product->listId = $listId;
        $product->name = $name;
        $product->price = $price;
        $product->description = $description;

        return $product;
    }

    public function collect(float $amount)
    {
        $this->alreadyCollected += $amount;
    }

    public function id(): Uuid
    {
        return $this->id;
    }
}
