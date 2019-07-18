<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Ramsey\Uuid\Uuid;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\AggregateChanged;

/**
 * For now Products will live inside List AR. We definitively need to think to move them in a dedicated AR ?
 */
final class ProductList extends AggregateRoot
{
    private $id;

    private $products = [];

    public static function start(Uuid $id)
    {
        $self = new self();
        $self->recordThat(
            ProductListWasStarted::record($id->toString())
        );

        return $self;
    }

    public function register(Uuid $id, string $name, float $price, string $description)
    {
        $this->recordThat(
            ProductWasRegistered::record($this->aggregateId(), $id->toString(), $name, $price, $description)
        );
    }

    public function collect(Uuid $id, float $amount)
    {
        $this->recordThat(
            MoneyWasCollected::occur($this->aggregateId(), ['product_id' => $id->toString(), 'amount' => $amount])
        );
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch ($event->messageName()) {
            case ProductListWasStarted::class:
                $this->whenProductListWasStarted($event);
                break;
            case ProductWasRegistered::class:
                $this->whenProductWasRegistered($event);
                break;
            case MoneyWasCollected::class:
                $this->whenMoneyWasCollected($event);
                break;
        }
    }

    private function whenProductListWasStarted(ProductListWasStarted $change)
    {
        $this->id = Uuid::fromString($change->aggregateId());
    }

    private function whenProductWasRegistered(ProductWasRegistered $change)
    {
        $this->products[] = Product::register(
            Uuid::fromString($change->aggregateId()),
            Uuid::fromString($change->id()),
            $change->name(),
            $change->price(),
            '',
            $change->description()
        );
    }

    private function whenMoneyWasCollected(MoneyWasCollected $change)
    {
        $productId = Uuid::fromString($change->productId());
        $product = array_reduce(
            $this->products,
            function ($carry, $item) use ($productId) {
                if ($item->id()->equals($productId)) {
                    return $item;
                }

                return $carry;
            },
            null
        );
        $product->collect($change->amount());
    }

}
