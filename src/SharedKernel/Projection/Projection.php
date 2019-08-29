<?php
declare(strict_types=1);

namespace App\SharedKernel\Projection;

final class Projection
{
    private $name;

    private $id;

    private $state = null;

    public function __construct(string $name, ?string $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function isEmpty(): bool
    {
        return null === $this->state;
    }

    public function id(): string
    {
        if (null === $this->id) {
            return $this->name;
        }

        return "{$this->name}-{$this->id}";
    }

    public function state()
    {
        return $this->state;
    }

    public function hash(): string
    {
        return sha1($this->name);
    }

    public function withState($state): self
    {
        $self = new self($this->name, $this->id);
        $self->state = $state;

        return $self;
    }
}
