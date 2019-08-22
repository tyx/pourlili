<?php
declare(strict_types=1);

namespace App\Listing\Domain;

class Host
{
    private $name;

    private $enabled;

    public function __construct(string $name, bool $enabled)
    {
        $this->name = $name;
        $this->enabled = $enabled;
    }

    public function __toString()
    {
        return "{$this->name}:{$this->enabled}";
    }

    public function name(): string
    {
        return $this->name;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }
}
