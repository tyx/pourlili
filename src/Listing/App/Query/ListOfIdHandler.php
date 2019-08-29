<?php
namespace App\Listing\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class ListOfIdHandler
{
    private $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ListOfId $query)
    {
        $state = $this->projector->load('all_list')->state();
        $listId = base64_encode($query->id()->toString());

        return array_reduce(
            $state['items'] ?? [],
            function ($carry, $item) use ($listId) {
                if ($item['id'] === $listId) {
                    return $item;
                }

                return $carry;
            },
            null
        );
    }
}
