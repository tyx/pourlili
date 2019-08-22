<?php
declare(strict_types=1);

namespace App\Listing\App\Command;

use Ramsey\Uuid\UuidInterface;

class EditList
{
    private $id;

    private $name;

    private $hosts;

    public function __construct(UuidInterface $id, string $name, array $hosts)
    {
        $this->id = $id;
        $this->name = $name;
        $this->hosts = $hosts;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function hosts(): array
    {
        return $this->hosts;
    }
}
