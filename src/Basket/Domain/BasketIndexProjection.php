<?php
declare(strict_types=1);

namespace App\Basket\Domain;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use App\SharedKernel\Projection\Projector;
use App\Basket\Domain\ProductWasAddedToBasket;
use App\Basket\Domain\BasketWasPickedUp;

/**
 * Need to find how prooph projection works. Until now, we have simple usecase, so we can do it manually
 */
class BasketIndexProjection implements MessageSubscriberInterface
{
    private $projector;

    private $catalogService;

    public function __construct(Projector $projector, CatalogService $catalogService)
    {
        $this->projector = $projector;
        $this->catalogService = $catalogService;
    }

    public function pickUpBasket(BasketWasPickedUp $event)
    {
        $this->projector->updateProjection(
            'basket_index',
            $event,
            function ($state, $event) {
                return [
                    'list_id' => $event->listId(),
                    'total'  => 0,
                    'items' => [],
                ];
            }
        );
    }

    public function handleProductWasAddedToBasket(ProductWasAddedToBasket $event)
    {
        $this->projector->updateProjection(
            'basket_index',
            $event,
            function ($state, $event) {
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
        );
    }

    public static function getHandledMessages(): iterable
    {
        yield BasketWasPickedUp::class => [
            'method' => 'pickUpBasket',
            'bus' => 'event.bus',
        ];

        yield ProductWasAddedToBasket::class => [
            'method' => 'handleProductWasAddedToBasket',
            'bus' => 'event.bus',
        ];
    }
}
