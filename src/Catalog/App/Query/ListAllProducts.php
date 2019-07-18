<?php
declare(strict_types=1);

namespace App\Catalog\App\Query;

class ListAllProducts
{
    private $wishListId;

    public function __construct(string $wishListId)
    {
        $this->wishListId = $wishListId;
    }

    public function wishListId(): string
    {
        return $this->wishListId;
    }
}
