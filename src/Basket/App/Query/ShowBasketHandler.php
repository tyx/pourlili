<?php
namespace App\Basket\App\Query;

use App\SharedKernel\Projection\Projector;

class ShowBasketHandler
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ShowBasket $query)
    {
        return $this->projector->load($query->basketId(), 'basket_index')->state();
    }
}