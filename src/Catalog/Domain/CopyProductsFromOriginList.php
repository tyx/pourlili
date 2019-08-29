<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use App\Catalog\App\Command\RegisterNewProduct;
use App\Listing\Domain\ProductListWasStarted;
use App\SharedKernel\Bridge\CommandBus;
use App\SharedKernel\Projection\ProjectionStore;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CopyProductsFromOriginList implements MessageSubscriberInterface
{
    private $projectionStore;

    private $commandBus;

    public function __construct(ProjectionStore $projectionStore, CommandBus $commandBus)
    {
        $this->projectionStore = $projectionStore;
        $this->commandBus = $commandBus;
    }

    public static function getHandledMessages(): iterable
    {
        yield ProductListWasStarted::class => [
            'method' => 'copyProducts',
            'bus' => 'event.bus',
        ];
    }

    public function copyProducts(ProductListWasStarted $event)
    {
        if (null === $event->originId()) {
            return;
        }

        $state = $this->projectionStore->load('product_list', $event->originId())->state();

        foreach ($state['items'] as $product) {
            $this->commandBus->execute(
                new RegisterNewProduct(
                    Uuid::fromString($event->aggregateId()),
                    Uuid::uuid4(),
                    $product['name'],
                    $product['price'],
                    null,
                    $product['description'],
                    $product['image']
                )
            );
        }
    }
}
