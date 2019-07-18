<?php
declare(strict_types=1);

namespace App\Basket\Infra;

use Ramsey\Uuid\Uuid;
use App\Basket\Domain\Basket;
use App\Basket\Domain\BasketRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class ProophBasketRepository extends AggregateRepository implements BasketRepository
{
    public function save(Basket $basket): void
    {
        $this->saveAggregateRoot($basket);
    }

    public function find(Uuid $id): ?Basket
    {
        return $this->getAggregateRoot($id->toString());
    }
}
