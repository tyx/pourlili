<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

/**
 * Need to find how prooph projection works. Until now, we have simple usecase, so we can do it manually
 */
class AllListProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('all_list')
            ->fromCategory('listing')
            ->when([
                ProductListWasStarted::class => function ($state, $event) {
                    $state['items'] = $state['items'] ?? [];

                    $state['items'][] = [
                        'id' => base64_encode($event->aggregateId()),
                        'host' => $event->host(),
                        'enabled' => false,
                    ];

                    return $state;
                },
                ProductListWasDisabled::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if (base64_encode($event->aggregateId()) === $item['id']) {
                                $item['enabled'] = false;
                            }

                            return $item;
                        },
                        $state['items']
                    );

                    return $state;
                },
                ProductListWasEnabled::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if (base64_encode($event->aggregateId()) === $item['id']) {
                                $item['enabled'] = true;
                            }

                            return $item;
                        },
                        $state['items']
                    );

                    return $state;
                },
            ])
            ->run()
        ;
    }
}
