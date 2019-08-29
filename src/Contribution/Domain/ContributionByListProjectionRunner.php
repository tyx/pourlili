<?php
declare(strict_types=1);

namespace App\Contribution\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class ContributionByListProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('contribution_list')
            ->fromCategory('contribution')
            ->partitionBy(function ($event) { return $event->listId(); })
            ->when([
                ContributionWasCheckedOut::class => function ($state, $event) {
                    $state['items'] = $state['items'] ?? [];

                    $state['items'][] = [
                        'id' => base64_encode($event->aggregateId()),
                        'amount' => $event->amount(),
                        'author' => $event->author(),
                        'basket_id' => $event->basketId(),
                        'status' => 'pending',
                    ];

                    return $state;
                },
                ContributionWasConfirmed::class => function ($state, $event) {
                    $state['items'] = array_map(
                        function ($item) use ($event) {
                            if ($item['id'] === base64_encode($event->aggregateId())) {
                                $item['status'] = 'confirmed';
                            }

                            return $item;
                        },
                        $state['items']
                    );

                    return $state;
                }
            ])
            ->run()
        ;
    }
}
