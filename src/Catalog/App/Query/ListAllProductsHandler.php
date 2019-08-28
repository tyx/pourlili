<?php
declare(strict_types=1);

namespace App\Catalog\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class ListAllProductsHandler
{
    private $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ListAllProducts $query): iterable
    {
        $list = $this->projector->load('product_list', $query->wishListId())->state();

        $items = $list['items'] ?? [];

        if (true === $query->onlyEnabled()) {
            $items = array_filter(
                $items,
                function ($item) {
                    if (array_key_exists('enabled', $item)) {
                        return $item['enabled'];
                    }

                    return true;
                }
            );
        }

        return $items;
    }
}
