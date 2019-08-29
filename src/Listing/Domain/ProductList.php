<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * For now Products will live inside List AR. We definitively need to think to move them in a dedicated AR ?
 */
final class ProductList extends AggregateRoot
{
    private $id;

    private $host;

    private $enabled = false;

    private $sortedProducts = [];

    private $originId;

    public static function start(?UuidInterface $id, string $host, ?UuidInterface $originId = null)
    {
        $self = new self();
        $self->recordThat(
            ProductListWasStarted::record($id->toString(), $host, $originId ? $originId->toString() : null)
        );

        return $self;
    }

    public function sortProducts(array $productIds)
    {
        if ($productIds == $this->sortedProducts) {
            return; // Already sorted
        }

        $this->recordThat(
            ProductListWasSorted::occur($this->aggregateId(), ['sort' => $productIds])
        );
    }

    public function disable()
    {
        $this->recordThat(
            ProductListWasDisabled::occur($this->aggregateId())
        );
    }

    public function enable()
    {
        $this->recordThat(
            ProductListWasEnabled::occur($this->aggregateId())
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
            case ProductListWasEnabled::class:
                $this->enabled = true;
                break;
            case ProductListWasDisabled::class:
                $this->enabled = false;
                break;
            case ProductListWasSorted::class:
                $this->sortedProducts = $event->sort();
                break;
        }
    }

    private function whenProductListWasStarted(ProductListWasStarted $change)
    {
        $this->id = Uuid::fromString($change->aggregateId());
        $this->host = $change->host();
        $this->enabled = false;
        $this->originId = $change->originId();
    }
}
