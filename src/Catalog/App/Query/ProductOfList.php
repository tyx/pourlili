<?php
declare(strict_types=1);

namespace App\Catalog\App\Query;

class ProductOfList
{
    private $listId;

    private $productId;

    public function __construct(string $listId, string $productId)
    {
        $this->listId = $listId;
        $this->productId = $productId;
    }

    public function listId(): string
    {
        return $this->listId;
    }

    public function productId(): string
    {
        return $this->productId;
    }
}
