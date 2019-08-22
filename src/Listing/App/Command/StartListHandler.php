<?php
namespace App\Listing\App\Command;

use App\Listing\Domain\Host;
use App\Listing\Domain\ProductList;
use App\Listing\Domain\ProductListRepository;

class StartListHandler
{
    private $productListRepository;

    public function __construct(ProductListRepository $productListRepository)
    {
        $this->productListRepository = $productListRepository;
    }

    public function __invoke(StartList $command)
    {
        $list = ProductList::start($command->id(), $command->name());
        $list->registerHosts(
            array_map(
                function ($item) { return new Host($item['name'], $item['enabled']); },
                $command->hosts()
            )
        );
        $this->productListRepository->save($list);
    }
}
