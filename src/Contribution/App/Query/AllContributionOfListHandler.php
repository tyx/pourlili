<?php
namespace App\Contribution\App\Query;

use App\SharedKernel\Projection\Projector;

class AllContributionOfListHandler
{
    protected $projector;

    public function __construct(Projector $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(AllContributionOfList $query)
    {
        return $this->projector->load($query->listId(), 'contribution_list')->state();
    }
}