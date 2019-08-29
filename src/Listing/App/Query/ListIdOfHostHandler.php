<?php
namespace App\Listing\App\Query;

use App\SharedKernel\Projection\ProjectionStore;

class ListIdOfHostHandler
{
    private $projector;

    public function __construct(ProjectionStore $projector)
    {
        $this->projector = $projector;
    }

    public function __invoke(ListIdOfHost $query)
    {
        $state = $this->projector->load('all_list')->state();
        $host = $query->host();

        return array_reduce(
            $state['items'] ?? [],
            function ($carry, $item) use ($host) {
                if ($item['host'] === $host && true === $item['enabled']) {
                    return $item['id'];
                }

                return $carry;
            },
            null
        );
    }
}
