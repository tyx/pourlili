<?php

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;

final class Product extends AggregateRoot
{
    private $id;

    private $listId;

    private $name;

    private $price;

    private $originalImagePath;

    private $uploadedImagePath;

    private $description;

    private $alreadyCollected = 0;

    public static function register(Uuid $id, Uuid $listId, string $name, float $price, string $description, ?string $imagePath)
    {
        $self = new static;
        $self->recordThat(
            ProductWasRegistered::record($id->toString(), $listId->toString(), $name, $price, $description, $imagePath)
        );

        return $self;
    }

    public function collect(float $amount)
    {
        $this->recordThat(
            MoneyWasCollected::occur($this->aggregateId(), ['amount' => $amount, 'list_id' => $this->listId->toString()])
        );
    }

    public function uploadImage(string $path)
    {
        $this->recordThat(
            ImageOfProductWasUploaded::occur($this->aggregateId(), ['path' => $path, 'list_id' => $this->listId->toString()])
        );
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch ($event->messageName()) {
            case ProductWasRegistered::class:
                $this->whenProductWasRegistered($event);
                break;
            case MoneyWasCollected::class:
                $this->whenMoneyWasCollected($event);
                break;
            case ImageOfProductWasUploaded::class:
                $this->uploadedImagePath = $event->path();
                break;
        }
    }

    private function whenProductWasRegistered(ProductWasRegistered $change)
    {
        $this->id = Uuid::fromString($change->aggregateId());
        $this->listId = Uuid::fromString($change->listId());
        $this->name = $change->name();
        $this->price = $change->price();
        $this->originalImagePath = $change->imagePath();
        $this->description = $change->description();
    }

    private function whenMoneyWasCollected(MoneyWasCollected $change)
    {
        $this->alreadyCollected += $change->amount();
    }
}
