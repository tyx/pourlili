<?php
namespace App\Catalog\App\Command;

use App\Catalog\Domain\ProductRepository;

class CollectMoneyForProductHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(CollectMoneyForProduct $command)
    {
        $product = $this->productRepository->find($command->productId());

        if (null === $product) {
            throw new \LogicException("No product with id {$command->productId()}");
        }

        $product->collect($command->amount());
        $this->productRepository->save($product);
    }
}