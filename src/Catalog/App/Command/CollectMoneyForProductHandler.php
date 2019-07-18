<?php
namespace App\Catalog\App\Command;

use App\Catalog\Domain\ProductListRepository;

class CollectMoneyForProductHandler
{
    private $productListRepository;

    public function __construct(ProductListRepository $productListRepository)
    {
        $this->productListRepository = $productListRepository;
    }

    public function __invoke(CollectMoneyForProduct $command)
    {
        $productList = $this->productListRepository->find($command->listId());

        if (null === $productList) {
            throw new \LogicException("No list with id {$command->listId()}");
        }

        $productList->collect($command->productId(), $command->amount());
        $this->productListRepository->save($productList);
    }
}