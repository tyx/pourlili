<?php
namespace App\Listing\App\Command;

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
        $this->productListRepository->save(
            ProductList::start($command->id(), $command->host())
        );
    }
}