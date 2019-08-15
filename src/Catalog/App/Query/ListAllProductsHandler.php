<?php
declare(strict_types=1);

namespace App\Catalog\App\Query;

use App\Catalog\Domain\ProductView;
use App\SharedKernel\Projection\Projector;

class ListAllProductsHandler
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ListAllProducts $query): iterable
    {
        ['items' => $items] = $this->projector->load($query->wishListId(), 'product_list')->state();

        return $items ?? [];
    }
}