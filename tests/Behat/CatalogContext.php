<?php
namespace App\Tests\Behat;

use App\Catalog\Domain\Product;
use App\Catalog\Domain\ProductList;
use App\Catalog\Domain\ProductListRepository;
use App\Catalog\Domain\ProductWasRegistered;
use App\Catalog\Infra\ProophProductRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\Uuid;

class CatalogContext implements Context
{
    private $eventStore;

    private $list;

    private $repository;

    private $uuid;

    public function __construct(EventStore $eventStore, ProductListRepository $productListRepository)
    {
        $this->eventStore = $eventStore;
        $this->repository = $productListRepository;
    }

    /**
     * @Given I started a baby list
     */
    public function iStartedABabyList()
    {
        $this->uuid = Uuid::uuid4();
        $this->list = ProductList::start($this->uuid);
    }

    /**
     * @When I register a new product named :name at price :price described by :description in my list
     */
    public function iRegisterANewProductNamedAtPriceDescribedByInMyCatalog(string $name, float $price, string $description)
    {
        $this->list->register(Uuid::uuid4(), $name, $price, $description);
        $this->repository->save($this->list);
    }

    /**
     * @Then my product named :name at price :price described by :description should be registered in my list
     */
    public function myProductNamedAtPriceDescribedByShouldBeInMyCatalog(string $name, float $price, string $description)
    {
        $changes = iterator_to_array($this->eventStore
            ->load(new StreamName(ProductList::class.'-'.$this->uuid->toString()))
        );

        expect($changes[1] ?? false)->toHaveType(ProductWasRegistered::class);
        expect($changes[1]->aggregateId())->toBe($this->uuid->toString());
        expect($changes[1]->name())->toBe($name);
        expect($changes[1]->price())->toBe($price);
        expect($changes[1]->description())->toBe($description);
    }
}
