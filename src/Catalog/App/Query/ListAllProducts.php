<?php
declare(strict_types=1);

namespace App\Catalog\App\Query;

class ListAllProducts
{
    private $wishListId;

    private $onlyEnabled;

    public function __construct(string $wishListId, bool $onlyEnabled = false)
    {
        $this->wishListId = $wishListId;
        $this->onlyEnabled = $onlyEnabled;
    }

    public function wishListId(): string
    {
        return $this->wishListId;
    }

    public function onlyEnabled(): bool
    {
        return $this->onlyEnabled;
    }
}
