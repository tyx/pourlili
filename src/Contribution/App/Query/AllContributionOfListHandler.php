<?php
namespace App\Contribution\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class AllContributionOfListHandler
{
    protected $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(AllContributionOfList $query)
    {
        return $this->projector->load('contribution_list', $query->listId())->state();
    }
}
