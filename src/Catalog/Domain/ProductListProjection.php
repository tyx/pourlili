<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\Catalog\Domain\ProductWasRegistered;
use App\Listing\Domain\ProductListWasSorted;
use App\SharedKernel\Projection\Projector;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * Need to find how prooph projection works. Until now, we have simple usecase, so we can do it manually
 */
class ProductListProjection implements MessageSubscriberInterface
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function handleProductWasRegistered(ProductWasRegistered $event)
    {
        $this->projector->updateProjection(
            'product_list',
            $event,
            function ($state, $event) {
                $state['items'] = $state['items'] ?? [];

                $state['items'][] = [
                    'id' => base64_encode($event->aggregateId()),
                    'name' => $event->name(),
                    'price' => $event->price(),
                    'image' => null,
                    'description' => $event->description(),
                    'funded' => false,
                    'alreadyCollected' => 0,
                    'remainingAmountToCollect' => $event->price(),
                    'progression' => 0,
                ];

                return $state;
            },
            $event->listId()
        );
    }

    public function handleMoneyWasCollected(MoneyWasCollected $event)
    {
        $this->projector->updateProjection(
            'product_list',
            $event,
            function ($state, $event) {
                $state['items'] = array_map(
                    function ($item) use ($event) {
                        if ($item['id'] === base64_encode($event->aggregateId())) {
                            $item['alreadyCollected'] += $event->amount();
                            $item['remainingAmountToCollect'] -= $event->amount();
                            $item['progression'] = round($item['alreadyCollected'] / $item['price']);
                        }

                        if ($item['price'] <= $item['alreadyCollected']) {
                            $item['funded'] = true;
                        }

                        return $item;
                    },
                    $state['items']
                );

                return $state;
            },
            $event->listId()
        );
    }

    public function handleProductImage(ImageOfProductWasUploaded $event)
    {
        $this->projector->updateProjection(
            'product_list',
            $event,
            function ($state, $event) {
                $state['items'] = array_map(
                    function ($item) use ($event) {
                        if ($item['id'] === base64_encode($event->aggregateId())) {
                            $item['image'] = $event->path();
                        }

                        return $item;
                    },
                    $state['items'] ?? []
                );

                return $state;
            },
            $event->listId()
        );
    }

    public function handleSortList(ProductListWasSorted $event)
    {
        $this->projector->updateProjection(
            'product_list',
            $event,
            function ($state, $event) {
                $state['items'] = $this->sortProducts($state['items'], $event->sort());
                dump($state['items']);
                return $state;
            }
        );
    }

    public static function getHandledMessages(): iterable
    {
        yield ProductWasRegistered::class => [
            'method' => 'handleProductWasRegistered',
            'bus' => 'event.bus',
        ];
        yield MoneyWasCollected::class => [
            'method' => 'handleMoneyWasCollected',
            'bus' => 'event.bus',
        ];
        yield ImageOfProductWasUploaded::class => [
            'method' => 'handleProductImage',
            'bus' => 'event.bus',
        ];
        // Leaking a DomainEvent of another module looks like a smell
        yield ProductListWasSorted::class => [
            'method' => 'handleSortList',
            'bus' => 'event.bus',
        ];
    }

    private function sortProducts($products, $newOrder)
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
