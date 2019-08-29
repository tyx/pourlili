<?php
declare(strict_types=1);

namespace App\Contribution\Domain;

use App\SharedKernel\Projection\ProjectionRunner;
use App\SharedKernel\Projection\Projector;

class GuestBookProjectionRunner implements ProjectionRunner
{
    public function __invoke(Projector $projector)
    {
        $projector->createProjection('guest_book')
            ->fromCategory('contribution')
            ->partitionBy(function ($event) { return $event->listId(); })
            ->when([
                ContributionWasSigned::class => function ($state, $event) {
                    $state['items'] = $state['items'] ?? [];

                    $state['items'][] = [
                        'signature' => $event->signature(),
                        'message' => $event->message(),
                        'signed_at' => $event->createdAt()->getTimestamp(),
                    ];

                    return $state;
                }
            ])
            ->run()
        ;
    }
}
