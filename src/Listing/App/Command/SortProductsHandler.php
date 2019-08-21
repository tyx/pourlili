<?php
declare(strict_types=1);

namespace App\Listing\App\Command;

use App\Listing\Domain\ProductListRepository;

class SortProductsHandler
{
    private $productListRepository;

    public function __construct(ProductListRepository $productListRepository)
    {
        $this->productListRepository = $productListRepository;
    }

    public function __invoke(SortProducts $command)
    {
        $list = $this->productListRepository->find($command->id());
        $list->sortProducts($command->sortedProductIds());
        $this->productListRepository->save($list);
    }
}
