<?php
namespace App\Contribution\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class GuestBookOfListHandler
{
    protected $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(GuestBookOfList $query)
    {
        return $this->projector->load('guest_book', $query->listId())->state();
    }
}
