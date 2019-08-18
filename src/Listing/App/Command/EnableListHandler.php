<?php
namespace App\Listing\App\Command;

use App\Listing\Domain\ProductListRepository;

class EnableListHandler
{
    private $productListRepository;

    public function __construct(ProductListRepository $productListRepository)
    {
        $this->productListRepository = $productListRepository;
    }

    public function __invoke(EnableList $command)
    {
        $list = $this->productListRepository->find($command->id());
        $list->enable();
        $this->productListRepository->save($list);
    }
}