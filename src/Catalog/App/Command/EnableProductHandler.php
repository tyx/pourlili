<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use App\Catalog\Domain\ProductRepository;

class EnableProductHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(EnableProduct $command)
    {
        $product = $this->productRepository->find($command->id());

        if (null === $product) {
            throw new \LogicException("Cannot find product with id : {$command->id()}");
        }

        $product->enable();

        $this->productRepository->save($product);
    }
}
