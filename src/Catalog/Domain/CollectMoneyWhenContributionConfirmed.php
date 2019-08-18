<?php
namespace App\Catalog\Domain;

use App\Catalog\App\Command\CollectMoneyForProduct;
use App\Contribution\Domain\ContributionWasConfirmed;
use App\SharedKernel\Bridge\CommandBus;
use App\SharedKernel\Projection\Projector;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class CollectMoneyWhenContributionConfirmed implements MessageSubscriberInterface
{
    private $projector;

    private $commandBus;

    public function __construct(Projector $projector, CommandBus $commandBus)
    {
        $this->projector = $projector;
        $this->commandBus = $commandBus;
    }

    public function handleContributionWasConfirmed(ContributionWasConfirmed $event)
    {
        $basket = $this->projector->load($event->basketId(), 'basket_index')->state();

        foreach ($basket['items'] as $product) {
            $this->commandBus->execute(
                new CollectMoneyForProduct(
                    Uuid::fromString(base64_decode($product['id'])),
                    $product['amount']
                )
            );
        }
    }

    public static function getHandledMessages(): iterable
    {
        yield ContributionWasConfirmed::class => [
            'method' => 'handleContributionWasConfirmed',
            'bus' => 'event.bus',
        ];
    }
}