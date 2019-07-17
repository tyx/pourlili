<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Ramsey\Uuid\Uuid;

interface ProductRepository
{
    public function find(Uuid $id): ?Product;

    public function save(Product $product): void;
}
