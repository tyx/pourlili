<?php
declare(strict_types=1);

namespace App\SharedKernel\Projection;

class Projection
{
    private $aggregateId;

    private $name;

    private $state;

    public function __construct($aggregateId, $name)
    {
        $this->aggregateId = $aggregateId;
        $this->name = $name;
    }

    public function loadState($state)
    {
        $this->state = $state;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function state()
    {
        return $this->state;
    }

    public function update($event, callable $updator)
    {
        $this->state = $updator($this->state, $event);
    }
}
