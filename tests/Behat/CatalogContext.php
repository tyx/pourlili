<?php
namespace App\Tests\Behat;

use App\Catalog\App\Command\RegisterNewProduct;
use App\Catalog\Domain\Product;
use App\Catalog\Domain\ProductList;
use App\Catalog\Domain\ProductPriceWasChanged;
use App\Catalog\Domain\ProductWasRegistered;
use App\Catalog\Domain\ProductWasRenamed;
use App\Listing\Domain\ProductListWasStarted;
use App\SharedKernel\Bridge\CommandBus;
use ArrayIterator;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PhpSpec\Matcher\MatchersProvider;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\Uuid;

class CatalogContext implements Context, MatchersProvider
{
    private $eventStore;

    private $uuid;

    private $productIds = [];

    private $commandBus;

    public function __construct(EventStore $eventStore, CommandBus $commandBus)
    {
        $this->eventStore = $eventStore;
        $this->commandBus = $commandBus;
    }

    /**
     * @Given I started a baby list
     */
    public function iStartedABabyList()
    {
        $this->uuid = Uuid::uuid4();
        $this->eventStore->create(
            new Stream(
                new StreamName("listing-{$this->uuid->toString()}"),
                new ArrayIterator(
                    [
                        ProductListWasStarted::record($this->uuid->toString(), 'localhost'),
                    ]
                )
            )
        );
    }

    /**
     * @When I register a new product named :name at price :price described by :description in my list
     */
    public function iRegisterANewProductNamedAtPriceDescribedByInMyCatalog(string $name, float $price, string $description)
    {
        $this->productIds[$name] = Uuid::uuid4();
        $this->commandBus->execute(new RegisterNewProduct($this->uuid, $this->productIds[$name], $name, $price, null, $description));
    }

    /**
     * @Then my product named :name at price :price described by :description should be registered in my list
     */
    public function myProductNamedAtPriceDescribedByShouldBeInMyCatalog(string $name, float $price, string $description)
    {
        $id = $this->productIds[$name];
        $expectedChanges = [
            ProductWasRegistered::record($id->toString(), $this->uuid->toString()),
            ProductWasRenamed::record($id->toString(), $this->uuid->toString(), $name),
            ProductPriceWasChanged::record($id->toString(), $this->uuid->toString(), $price),
        ];
        $changes = iterator_to_array(
            $this->eventStore
            ->load(new StreamName("product-{$id->toString()}"))
        );
        foreach ($expectedChanges as $change) {
            expect($changes)->toContainChange($change);
        }
    }

    public function getMatchers(): array
    {
        return [
            'containChange' => function ($sut, $expectation) {
                $past = array_map(
                    function ($item) {
                        $serialized = $item->toArray();
                        unset(
                            $serialized['created_at'],
                            $serialized['uuid'],
                            $serialized['metadata']['_aggregate_type'],
                            $serialized['metadata']['_aggregate_version']
                        );

                        return $serialized;
                    },
                    $sut
                );

                $expected = $expectation->toArray();
                unset($expected['created_at'], $expected['uuid'], $expected['metadata']['_aggregate_version']);

                return in_array($expected, $past, true);
            },
        ];
    }
}
