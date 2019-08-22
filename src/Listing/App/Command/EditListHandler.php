<?php
declare(strict_types=1);

namespace App\Listing\App\Command;

use App\Listing\Domain\Host;
use App\Listing\Domain\ProductListRepository;

class EditListHandler
{
    private $productListRepository;

    public function __construct(ProductListRepository $productListRepository)
    {
        $this->productListRepository = $productListRepository;
    }

    public function __invoke(EditList $command)
    {
        dump($command);
        $productList = $this->productListRepository->find($command->id());

        if (null === $productList) {
            throw new \LogicException("No product list found with id {$command->id()}");
        }

        $productList->changeName($command->name());
        $productList->registerHosts(
            array_map(
                function ($item) { return new Host($item['name'], $item['enabled']); },
                $command->hosts()
            )
        );

        $this->productListRepository->save($productList);
    }
}
