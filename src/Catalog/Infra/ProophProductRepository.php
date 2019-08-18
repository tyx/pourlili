<?php
declare(strict_types=1);

namespace App\Catalog\Infra;

use App\Catalog\Domain\Product;
use App\Catalog\Domain\ProductRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\Uuid;

class ProophProductRepository extends AggregateRepository implements ProductRepository
{
    public function save(Product $product): void
    {
        $this->saveAggregateRoot($product);
    }

    public function find(Uuid $id): ?Product
    {
        return $this->getAggregateRoot($id->toString());
    }
}
