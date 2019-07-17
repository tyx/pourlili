<?php
declare(strict_types=1);

namespace App\Catalog\Infra;

use App\Catalog\Domain\Product;
use App\Catalog\Domain\ProductRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\EventStore;
use Ramsey\Uuid\Uuid;

class ProophProductRepository extends AggregateRepository implements ProductRepository
{
    public function __construct(EventStore $eventStore)
    {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass(Product::class),
            new AggregateTranslator(),
            null, //We don't use a snapshot store in the example
            null, //Also a custom stream name is not required
            true //But we enable the "one-stream-per-aggregate" mode
        );
    }

    public function save(Product $product): void
    {
        $this->saveAggregateRoot($product);
    }

    public function find(Uuid $id): ?Product
    {
        return $this->getAggregateRoot($uuid->toString());
    }
}
