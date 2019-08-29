<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class ProductListProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('product_list')
            ->fromCategory('listing')
            ->forEachStream()
            ->when([
                ProductListWasSorted::class => function ($state, $event) {
                    $state['items'] = $this->sortProducts($state['items'], $event->sort());

                    return $state;
                }
            ])
            ->run()
        ;
    }

    private function sortProducts(array $products, array $newOrder): array
    {
        usort(
            $products,
            function ($a, $b) use ($newOrder) {
                $indexA = array_search($a['id'], $newOrder, true);
                $indexB = array_search($b['id'], $newOrder, true);

                return $indexA <=> $indexB;
            }
        );

        return $products;
    }
}
