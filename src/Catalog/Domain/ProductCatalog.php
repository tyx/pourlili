<?php
declare(strict_types=1);

namespace App\Catalog\Domain;

use Ramsey\Uuid\Uuid;

class ProductCatalog
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function register(Uuid $id, $name, $price, $description)
    {
        $this->productRepository->save(
            Product::register($id, $name, $price, '', $description)
        );
    }
}
