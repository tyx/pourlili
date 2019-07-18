<?php
declare(strict_types=1);

namespace App\Basket\Domain;

use Ramsey\Uuid\Uuid;

interface BasketRepository
{
    public function find(Uuid $id): ?Basket;

    public function save(Basket $basket): void;
}
