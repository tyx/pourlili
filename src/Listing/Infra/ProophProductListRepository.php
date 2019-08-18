<?php
declare(strict_types=1);

namespace App\Listing\Infra;

use App\Listing\Domain\ProductList;
use App\Listing\Domain\ProductListRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\Uuid;

class ProophProductListRepository extends AggregateRepository implements ProductListRepository
{
    public function save(ProductList $list): void
    {
        $this->saveAggregateRoot($list);
    }

    public function find(Uuid $id): ?ProductList
    {
        return $this->getAggregateRoot($id->toString());
    }
}
