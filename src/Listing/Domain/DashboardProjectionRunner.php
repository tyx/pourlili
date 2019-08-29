<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use App\Catalog\Domain\ProductWasRegistered;
use App\Contribution\Domain\ContributionWasConfirmed;
use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class DashboardProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('dashboard')
            ->fromCategories(['product', 'contribution'])
            ->partitionBy(function ($event) { return $event->listId(); })
            ->when([
                '$init' => [
                    'products' => [
                        'total' => 0,
                    ],
                    'contributions' => [
                        'total' => 0,
                    ],
                    'money' => [
                        'total' => 0,
                    ],
                ],
                ContributionWasConfirmed::class => function ($state, $event) {
                    $state['contributions']['total'] += 1;
                    $state['money']['total'] += $event->amount();

                    return $state;
                },
                ProductWasRegistered::class => function ($state, $event) {
                    $state['products']['total'] += 1;

                    return $state;
                }
            ])
            ->run()
        ;
    }
}
