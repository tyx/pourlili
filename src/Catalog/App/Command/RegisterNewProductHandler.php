<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use App\Catalog\Domain\Product;
use App\Catalog\Domain\ProductRepository;

class RegisterNewProductHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(RegisterNewProduct $command)
    {
        $product = Product::register(
            $command->id(),
            $command->listId(),
            $command->name(),
            $command->price(),
            $command->description(),
            $command->image()
        );
        $this->productRepository->save($product);
    }
}
