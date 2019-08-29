<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class ProductListProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('product_list')
            ->fromCategory('product')
            ->partitionBy(function ($event) { return $event->listId(); })
            ->when([
                ProductWasRegistered::class => function (array $state, $event) {
                    $state['items'] = $state['items'] ?? [];

                    $state['items'][] = [
                        'id' => base64_encode($event->aggregateId()),
                        'name' => null,
                        'price' => null,
                        'image' => null,
                        'description' => null,
                        'funded' => false,
                        'alreadyCollected' => 0,
                        'remainingAmountToCollect' => null,
                        'progression' => 0,
                        'enabled' => true,
                    ];

                    return $state;
                },
                MoneyWasCollected::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] !== base64_encode($event->aggregateId())) {
                                return $item;
                            }
                            $item['alreadyCollected'] += $event->amount();

                            if (null === $item['price']) {
                                return $item;
                            }

                            $item['remainingAmountToCollect'] -= $event->amount();

                            if ($item['price'] < $item['alreadyCollected']) {
                                $item['alreadyCollected'] = $item['price'];
                            }

                            if ($item['remainingAmountToCollect'] < 0) {
                                $item['remainingAmountToCollect'] = 0;
                            }

                            if ($item['price'] <= $item['alreadyCollected']) {
                                $item['funded'] = true;
                            }

                            $item['progression'] = round($item['alreadyCollected'] / $item['price']);

                            return $item;
                        },
                        $state['items']
                    );

                    return $state;
                },
                ImageOfProductWasUploaded::class => function ($state, $event) {
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
                ProductWasRenamed::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] === base64_encode($event->aggregateId())) {
                                $item['name'] = $event->name();
                            }

                            return $item;
                        },
                        $state['items'] ?? []
                    );

                    return $state;
                },
                ProductWasDescribed::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] === base64_encode($event->aggregateId())) {
                                $item['description'] = $event->description();
                            }

                            return $item;
                        },
                        $state['items'] ?? []
                    );

                    return $state;
                },
                ProductWasEnabled::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] === base64_encode($event->aggregateId())) {
                                $item['enabled'] = true;
                            }

                            return $item;
                        },
                        $state['items'] ?? []
                    );

                    return $state;
                },
                ProductWasDisabled::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] === base64_encode($event->aggregateId())) {
                                $item['enabled'] = false;
                            }

                            return $item;
                        },
                        $state['items'] ?? []
                    );

                    return $state;
                },
                ProductPriceWasChanged::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] === base64_encode($event->aggregateId())) {
                                $item['remainingAmountToCollect'] = $item['price'] = $event->price();
                            }

                            return $item;
                        },
                        $state['items'] ?? []
                    );

                    return $state;
                },
            ])
            ->run()
        ;
    }
}
