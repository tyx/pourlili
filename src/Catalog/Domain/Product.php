<?php

namespace App\Catalog\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;

/**
 * Dedicated aggregate because product could have many changes.
 * But we need to improve ProductList and Product collaboration.
 * Currently the only way to build a projection to list all products is to add list_id in all events.
 */
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

    private $enabled = true;

    public static function register(Uuid $id, Uuid $listId, string $name, ?float $price, string $description, ?string $imagePath, ?string $uploadedPath = null)
    {
        $self = new static;
        $self->recordThat(
            ProductWasRegistered::record($id->toString(), $listId->toString())
        );
        $self->rename($name);
        $self->describe($description);
        if (null !== $price) {
            $self->changePrice($price);
        }
        if (null !== $imagePath) {
            $self->changeImage($imagePath);
        };
        if (null !== $uploadedPath) {
            $self->uploadImage($uploadedPath);
        }

        return $self;
    }

    public function collect(float $amount)
    {
        if ($this->isFunded()) {
            throw new \LogicException('Product is already funded');
        }

        $this->recordThat(
            MoneyWasCollected::occur($this->aggregateId(), ['amount' => $amount, 'list_id' => $this->listId->toString()])
        );
    }

    public function rename(string $newName)
    {
        if ($this->name === $newName) {
            return;
        }

        $this->recordThat(
            ProductWasRenamed::occur($this->aggregateId(), ['list_id' => $this->listId->toString(), 'new_name' => $newName])
        );
    }

    public function describe(string $newDescription)
    {
        if ($this->description === $newDescription) {
            return;
        }

        $this->recordThat(
            ProductWasDescribed::occur($this->aggregateId(), ['list_id' => $this->listId->toString(), 'new_description' => $newDescription])
        );
    }

    public function changePrice(float $newPrice)
    {
        if ($this->price === $newPrice) {
            return;
        }

        if (0 < $this->alreadyCollected) {
            throw new \LogicException('Cannot change price of product with money already collected');
        }

        $this->recordThat(
            ProductPriceWasChanged::occur($this->aggregateId(), ['list_id' => $this->listId->toString(), 'new_price' => $newPrice])
        );
    }

    public function changeImage(string $originalPath)
    {
        if ($this->originalImagePath === $originalPath) {
            return;
        }

        $this->recordThat(
            ProductImageWasChanged::occur($this->aggregateId(), ['list_id' => $this->listId->toString(), 'new_original_image' => $originalPath])
        );
    }

    public function uploadImage(string $path)
    {
        $this->recordThat(
            ImageOfProductWasUploaded::occur($this->aggregateId(), ['path' => $path, 'list_id' => $this->listId->toString()])
        );
    }

    public function disable()
    {
        if (false === $this->enabled) {
            return;
        }

        $this->recordThat(
            ProductWasDisabled::occur($this->aggregateId(), ['list_id' => $this->listId->toString()])
        );
    }

    public function enable()
    {
        if (true === $this->enabled) {
            return;
        }

        $this->recordThat(
            ProductWasEnabled::occur($this->aggregateId(), ['list_id' => $this->listId->toString()])
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
                $this->id = Uuid::fromString($event->aggregateId());
                $this->listId = Uuid::fromString($event->listId());
                break;
            case ProductWasRenamed::class:
                $this->name = $event->name();
                break;
            case ProductPriceWasChanged::class:
                $this->price = $event->price();
                break;
            case ProductWasDescribed::class:
                $this->description = $event->description();
                break;
            case ProductImageWasChanged::class:
                $this->originalImagePath = $event->imagePath();
                break;
            case ProductWasDisabled::class:
                $this->enabled = false;
                break;
            case ProductWasEnabled::class:
                $this->enabled = true;
                break;
            case MoneyWasCollected::class:
                $this->alreadyCollected += $event->amount();
                break;
            case ImageOfProductWasUploaded::class:
                $this->uploadedImagePath = $event->path();
                break;
        }
    }

    private function isFunded()
    {
        return $this->price <= $this->alreadyCollected;
    }
}
