<?php

use App\Catalog\Domain\Product;
use App\Catalog\Domain\ProductCatalog;
use App\Catalog\Domain\ProductWasRegistered;
use App\Catalog\Infra\ProophProductRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\StreamName;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Ramsey\Uuid\Uuid;

class CatalogContext implements Context
{
    private $eventStore;

    private $catalog;

    private $repository;

    private $uuid;

    public function __construct()
    {
        $this->eventStore = new TransactionalActionEventEmitterEventStore(
            new InMemoryEventStore(),
            new ProophActionEventEmitter()
        );
        $this->repository =  new ProophProductRepository($this->eventStore);
        $this->catalog = new ProductCatalog($this->repository);
    }
    /**
     * @When I register a new product named :name at price :price described by :description in my catalog
     */
    public function iRegisterANewProductNamedAtPriceDescribedByInMyCatalog(string $name, float $price, string $description)
    {
        $this->uuid = Uuid::uuid4();
        $this->catalog->register($this->uuid, $name, $price, $description);
    }

    /**
     * @Then my product named :name at price :price described by :description should be registered in my catalog
     */
    public function myProductNamedAtPriceDescribedByShouldBeInMyCatalog(string $name, float $price, string $description)
    {
        $changes = $this->eventStore
            ->load(new StreamName(Product::class.'-'.$this->uuid->toString()))
            ->getArrayCopy()
        ;

        expect($changes[0] ?? false)->toHaveType(ProductWasRegistered::class);
        expect($changes[0]->aggregateId())->toBe($this->uuid->toString());
        expect($changes[0]->payload())->toBe([
            'name' => $name,
            'price' => $price,
            'description' => $description,
        ]);
    }
}
