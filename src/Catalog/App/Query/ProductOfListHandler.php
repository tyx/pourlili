<?php
declare(strict_types=1);

namespace App\Catalog\App\Query;

use App\Catalog\Domain\ProductListProjection;
use App\SharedKernel\Projection\Projector;

class ProductOfListHandler
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ProductOfList $query)
    {
        ['items' => $items] = $this->projector->load($query->listId(), ProductListProjection::NAME)->state();
        $product = array_reduce(
            $items,
            function ($carry, $item) use ($query) {
                if ($query->productId() === $item['id']) {
                    return $item;
                }

                return $carry;
            }
        );

        if (null === $product) {
            throw new \LogicException("No product found with id {$query->productId()}");
        }

        return $product;
    }
}
