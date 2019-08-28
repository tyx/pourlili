<?php
namespace App\SharedKernel\Projection;

class Projections
{
    private $projectionStore;

    private $collection;

    public function __construct(ProjectionStore $projectionStore, iterable $collection)
    {
        $this->projectionStore = $projectionStore;
        $this->collection = $collection;
    }

    public function run($streamName, iterable $events)
    {
        foreach ($this->collection as $projection) {
            $projection(new Projector($this->projectionStore, $streamName, $events));
        }
    }
}
