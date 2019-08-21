<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use App\Catalog\Domain\ProductRepository;

class UploadImageOfProductHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(UploadImageOfProduct $command)
    {
        $product = $this->productRepository->find($command->id());
        $product->uploadImage($command->imagePath());
        $this->productRepository->save($product);
    }
}
