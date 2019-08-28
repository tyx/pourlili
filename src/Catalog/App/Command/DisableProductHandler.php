<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use App\Catalog\Domain\ProductRepository;

class DisableProductHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(DisableProduct $command)
    {
        $product = $this->productRepository->find($command->id());

        if (null === $product) {
            throw new \LogicException("Cannot find product with id : {$command->id()}");
        }

        $product->disable();

        $this->productRepository->save($product);
    }
}
