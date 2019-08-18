<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Ramsey\Uuid\Uuid;

interface ProductListRepository
{
    public function find(Uuid $id): ?ProductList;

    public function save(ProductList $list): void;
}
