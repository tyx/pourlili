<?php
namespace App\Basket\App\Command;

use App\Basket\Domain\CatalogService;
use App\Basket\Domain\BasketRepository;

class AddProductToBasketHandler
{
    private $basketRepository;

    private $catalogService;

    public function __construct(BasketRepository $basketRepository, CatalogService $catalogService)
    {
        $this->basketRepository = $basketRepository;
        $this->catalogService = $catalogService;
    }

    public function __invoke(AddProductToBasket $command)
    {
        $basket = $this->basketRepository->find($command->basketId());

        if (null === $basket) {
            throw new \LogicException("No basket found with id {$command->basketId()}");
        }

        $product = $this->catalogService->productOfList($basket->listId(), $command->productId());

        if (null === $product) {
            throw new \LogicException("No basket found with id {$command->productId()}");
        }

        $basket->addProduct($command->productId(), $command->amountOfContribution());

        $this->basketRepository->save($basket);
    }
}