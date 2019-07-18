<?php
namespace App\Basket\Domain;

use Ramsey\Uuid\Uuid;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\AggregateChanged;

final class Basket extends AggregateRoot
{
    private $id;

    private $listId;

    private $products = [];

    public static function pickUp(Uuid $id, string $listId): self
    {
        $self = new self();
        $self->recordThat(
           BasketWasPickedUp::occur($id->toString(), ['list_id' => $listId])
        );

        return $self;
    }

    public function listId()
    {
        return $this->listId;
    }

    public function addProduct(string $productId, float $amountOfContribution)
    {
        $this->recordThat(
            ProductWasAddedToBasket::occur($this->id->toString(), ['product_id' => $productId, 'amount_contribution' => $amountOfContribution])
        );
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch ($event->messageName()) {
            case BasketWasPickedUp::class: $this->whenBasketWasPickedUp($event); break;
            case ProductWasAddedToBasket::class: $this->whenProductWasAddedToBasket($event); break;
        }
    }

    private function whenBasketWasPickedUp(BasketWasPickedUp $change)
    {
        $this->id = Uuid::fromString($change->aggregateId());
        $this->listId = $change->listId();
    }

    private function whenProductWasAddedToBasket(ProductWasAddedToBasket $change)
    {
        $this->products[] = new Product($change->productId(), $change->amountOfContribution());
    }
}
