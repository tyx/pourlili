<?php
declare(strict_types=1);

namespace App\Listing\Domain;

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

            $state['items'][] = [
                'id' => base64_encode($event->aggregateId()),
                'host' => $event->host(),
                'enabled' => false,
            ];

            return $state;
        });
        $this->projector->save($projection);
    }

    public function disableProductList(ProductListWasDisabled $event)
    {
        $this->projector->updateProjection(
            'all_list',
            $event,
            function ($state, $event) {
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
            ''
        );
    }

    public function enableProductList(ProductListWasEnabled $event)
    {
        $this->projector->updateProjection(
            'all_list',
            $event,
            function ($state, $event) {
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
            ''
        );
    }

    public static function getHandledMessages(): iterable
    {
        yield ProductListWasStarted::class => [
            'method' => 'aggregateAllListIds',
            'bus' => 'event.bus',
        ];

        yield ProductListWasDisabled::class => [
            'method' => 'disableProductList',
            'bus' => 'event.bus'
        ];

        yield ProductListWasEnabled::class => [
            'method' => 'enableProductList',
            'bus' => 'event.bus'
        ];
    }
}
