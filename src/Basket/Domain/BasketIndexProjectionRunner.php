<?php
declare(strict_types=1);

namespace App\Basket\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class BasketIndexProjectionRunner implements ProjectionRunner
{
    private $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function __invoke(Projector $projector)
    {
        $projector->createProjection('basket_index')
            ->fromCategory('basket')
            ->forEachStream()
            ->when([
                BasketWasPickedUp::class => function ($state, $event) {
                    return [
                        'list_id' => $event->listId(),
                        'total'  => 0,
                        'items' => [],
                    ];
                },
                ProductWasAddedToBasket::class => function ($state, $event) {
                    // Are we sure it is the best way ?
                    $product = $this->catalogService->productOfList($state['list_id'], $event->productId());
                    $state['total'] += $event->amountOfContribution();
                    $state['items'][] = [
                        'id' => $event->productId(),
                        'name' => $product['name'],
                        'image' => $product['image'],
                        'amount' => $event->amountOfContribution(),
                    ];

                    return $state;
                }
            ])
            ->run()
        ;
    }
}
