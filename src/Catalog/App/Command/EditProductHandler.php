<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use App\Catalog\Domain\ProductRepository;

class EditProductHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(EditProduct $command)
    {
        $product = $this->productRepository->find($command->id());

        if (null === $product) {
            throw new \LogicException("Cannot find product with id : {$command->id()}");
        }

        $product->rename($command->name());
        $product->describe($command->description());
        if (null !== $command->price()) {
            $product->changePrice($command->price());
        }
        if (null !== $command->image()) {
            $product->changeImage($command->image());
        }

        $this->productRepository->save($product);
    }
}
