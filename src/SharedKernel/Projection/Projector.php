<?php
declare(strict_types=1);

namespace App\SharedKernel\Projection;

class Projector
{
    private $projectionStore;

    private $streamName;

    private $events;

    private $aggregateId;

    private $category;

    private $shouldBeRun = false;

    private $eachStream = false;

    private $name;

    private $partitionBy;

    public function __construct(ProjectionStore $projectionStore, string $streamName, iterable $events)
    {
        $this->projectionStore = $projectionStore;
        $this->streamName = $streamName;
        $this->aggregateId = $this->extractAggregateId($streamName);
        $this->category = $this->extractCategory($streamName);
        $this->events = $events;
    }

    public function createProjection($name)
    {
        $this->name = $name;

        return $this;
    }

    public function fromCategory(string $category)
    {
        $this->shouldBeRun = ($this->category === $category);

        return $this;
    }

    public function forEachStream()
    {
        $this->eachStream = true;

        return $this;
    }

    public function when(array $handlers)
    {
        $this->handlers = $handlers;

        return $this;
    }

    public function partitionBy(callable $partitionBy)
    {
        $this->partitionBy = $partitionBy;

        return $this;
    }

    public function run()
    {
        if (false === $this->shouldBeRun) {
            return;
        }

        foreach ($this->events as $event) {
            if (false === array_key_exists($event->messageName(), $this->handlers)) {
                continue;
            }
            $projection = $this->loadProjection($event);
            $handler = $this->handlers[$event->messageName()];
            $state = $handler($projection->state(), $event);
            $this->projectionStore->commit(
                $projection->withState($state)
            );
        }

        $this->projectionStore->flush();
    }

    private function loadProjection($event)
    {
        $projectionId = $this->eachStream ? $this->aggregateId : null;

        if (null !== $this->partitionBy) {
            $projectionId = call_user_func_array($this->partitionBy, [$event]);
        }

        return $this->projectionStore->load($this->name, $projectionId);
    }

    private function extractAggregateId(string $streamName): string
    {
        $result = substr($streamName, strpos($streamName, '-') + 1);

        if (false === $result) {
            throw new \LogicException("Cannot read aggregate id from {$streamName}");
        }

        return $result;
    }

    private function extractCategory(string $streamName): string
    {
        $result = substr($streamName, 0, strpos($streamName, '-'));

        if (false === $result) {
            throw new \LogicException("Cannot read category from {$streamName}");
        }

        return $result;
    }
}
