<?php

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;

final class Product extends AggregateRoot
{
    private $id;

    private $name;

    private $price;

    private $imagePath;

    private $description;

    private $alreadyCollected = 0;

    public static function register(Uuid $id, string $name, float $price, string $imagePath, string $description)
    {
        $product = new self;
        $product->recordThat(
            ProductWasRegistered::record($id->toString(), $name,  $price, $description)
        );

        return $product;
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case ProductWasRegistered::class:
                $this->whenProductWasRegistered($event);
                break;
        }
    }

    private function whenProductWasRegistered(ProductWasRegistered $change)
    {
        $this->id = Uuid::fromString($change->aggregateId());
        $this->name = $change->name();
        $this->price = $change->price();
        $this->description = $change->description();
    }
}
