<?php
namespace App\Listing\App\Command;

use App\Listing\Domain\ProductListRepository;

class DisableListHandler
{
    private $productListRepository;

    public function __construct(ProductListRepository $productListRepository)
    {
        $this->productListRepository = $productListRepository;
    }

    public function __invoke(DisableList $command)
    {
        $list = $this->productListRepository->find($command->id());
        $list->disable();
        $this->productListRepository->save($list);
    }
}