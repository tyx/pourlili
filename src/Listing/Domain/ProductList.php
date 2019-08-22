<?php
declare(strict_types=1);

namespace App\Listing\Domain;

use Ramsey\Uuid\Uuid;
use Prooph\EventSourcing\AggregateRoot;
use Prooph\EventSourcing\AggregateChanged;

/**
 * For now Products will live inside List AR. We definitively need to think to move them in a dedicated AR ?
 */
final class ProductList extends AggregateRoot
{
    private $id;

    private $enabled = false;

    private $sortedProducts = [];

    private $hosts = [];

    private $name;

    public static function start(Uuid $id, string $name)
    {
        $self = new self();
        $self->recordThat(
            ProductListWasStarted::record($id->toString(), $name)
        );

        return $self;
    }

    public function changeName(string $newName)
    {
        if ($newName === $this->name) {
            return;
        }

        $this->recordChange(
            ProductListNameWasChanged::occur($this->aggregateId(), ['new_name' -> $this->$newName])
        );
    }

    /**
     * Will replace all hosts with given ones
     */
    public function registerHosts(array $newHosts)
    {
        $diff = Diff::compute($this->hosts, $newHosts);
dump($diff); exit;
        foreach ($diff->additions() as $newHost) {
            $this->recordThat(
                HostWasRegisteredOnList::occur($this->aggregateId(), ['host_name' => $newHost->name(), 'host_enabled' => $newHost->enabled()])
            );
        }

        foreach ($diff->deletions() as $oldHost) {
            $this->recordThat(
                HostWasUnregisteredFromList::occur($this->aggregateId(), ['host_name' => $oldHost->name(), 'host_enabled' => $oldHost->enabled()])
            );
        }
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
            case HostWasRegisteredOnList::class:
                $this->hosts[] = new Host($event->hostName(), $event->hostEnabled());
                break;
        }
    }

    private function whenProductListWasStarted(ProductListWasStarted $change)
    {
        $this->id = Uuid::fromString($change->aggregateId());
        $this->name = $change->name();
        $this->enabled = false;
        $this->hosts = [];
    }
}
