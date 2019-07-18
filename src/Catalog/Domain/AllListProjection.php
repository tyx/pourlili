<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\Catalog\Domain\ProductWasRegistered;
use App\SharedKernel\Projection\Projector;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * Need to find how prooph projection works. Until now, we have simple usecase, so we can do it manually
 */
class AllListProjection implements MessageSubscriberInterface
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function aggregateAllListIds(ProductListWasStarted $event)
    {
        $projection = $this->projector->load('', 'all_list');
        $projection->update($event, function ($state, $event) {
            $state['items'] = $state['items'] ?? [];

            $state['items'][] = base64_encode($event->aggregateId());

            return $state;
        });
        $this->projector->save($projection);
    }

    public static function getHandledMessages(): iterable
    {
        yield ProductListWasStarted::class => [
            'method' => 'aggregateAllListIds',
            'bus' => 'event.bus',
        ];
    }
}
