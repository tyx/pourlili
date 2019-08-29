<?php
declare(strict_types=1);

namespace App\Listing\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class DashboardOfListHandler
{
    private $projectionStore;

    public function __construct(ProjectionStore $projectionStore)
    {
        $this->projectionStore = $projectionStore;
    }

    public function __invoke(DashboardOfList $query)
    {
        return $this->projectionStore->load('dashboard', $query->listId())->state();
    }
}
