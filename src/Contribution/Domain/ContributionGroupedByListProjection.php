<?php
declare(strict_types=1);

namespace App\Contribution\Domain;

use App\Contribution\Domain\ContributionWasCheckedOut;
use App\SharedKernel\Projection\Projector;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class ContributionGroupedByListProjection implements MessageSubscriberInterface
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public static function getHandledMessages(): iterable
    {
        yield ContributionWasCheckedOut::class => [
            'method' => 'checkout',
            'bus' => 'event.bus',
        ];

        yield ContributionWasConfirmed::class => [
            'method' => 'confirm',
            'bus' => 'event.bus',
        ];
    }

    public function checkout(ContributionWasCheckedOut $event)
    {
        $this->projector->updateProjection(
            'contribution_list',
            $event,
            function ($state, $event) {
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
            $event->listId()
        );
    }

    public function confirm(ContributionWasConfirmed $event)
    {
        $this->projector->updateProjection(
            'contribution_list',
            $event,
            function ($state, $event) {
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
            },
            $event->listId()
        );
    }
}