<?php
namespace App\Basket\Infra;

use App\SharedKernel\Bridge\QueryBus;
use App\Catalog\App\Query\ListAllProducts;
use App\Basket\Domain\CatalogService;

class DirectCatalogService implements CatalogService
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function productOfList($listId, $productId)
    {
        return array_reduce(
            $this->queryBus->query(new ListAllProducts($listId)),
            function ($carry, $item) use ($productId) {
                if ($item['id'] === $productId) {
                    return $item;
                };

                return $carry;
            },
            null
        );
    }
}