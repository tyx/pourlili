<?php
namespace App\Basket\App\Command;

use App\Basket\Domain\Basket;
use App\Basket\Domain\BasketRepository;

class PickUpBasketHandler
{
    private $basketRepository;

    public function __construct(BasketRepository $basketRepository)
    {
        $this->basketRepository = $basketRepository;
    }

    public function __invoke(PickUpBasket $command)
    {
        // Need to check list exists
        $this->basketRepository->save(
            Basket::pickUp($command->basketId(), $command->listId())
        );
    }
}