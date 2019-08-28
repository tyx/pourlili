<?php
namespace App\Basket\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class ShowBasketHandler
{
    private $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ShowBasket $query)
    {
        return $this->projector->load('basket_index', $query->basketId())->state();
    }
}
