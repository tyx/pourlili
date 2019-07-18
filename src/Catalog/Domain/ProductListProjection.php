<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\Catalog\Domain\ProductWasRegistered;
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
                    'id' => base64_encode($event->id()),
                    'name' => $event->name(),
                    'price' => $event->price(),
                    'image' => '',
                    'description' => $event->description(),
                    'funded' => false,
                    'alreadyCollected' => 0,
                    'remainingAmountToCollect' => $event->price(),
                ];

                return $state;
            }
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
                        if ($item['id'] === base64_encode($event->productId())) {
                            $item['alreadyCollected'] += $event->amount();
                            $item['remainingAmountToCollect'] -= $event->amount();
                            $item['progression'] = round($item['alreadyCollected'] / $item['price']) * 100;
                        }

                        if ($item['price'] <= $item['alreadyCollected']) {
                            $item['funded'] = true;
                        }

                        return $item;
                    },
                    $state['items']
                );

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
    }
}
