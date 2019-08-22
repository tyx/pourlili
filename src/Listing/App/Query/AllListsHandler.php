<?php
namespace App\Listing\App\Query;

use App\SharedKernel\Projection\Projector;

class AllListsHandler
{
    private $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(AllLists $query)
    {
        $state = $this->projector->load('', 'all_list')->state() ?? ['items' => []];

        return $state['items'];
    }
}
