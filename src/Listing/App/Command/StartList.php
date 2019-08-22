<?php
namespace App\Listing\App\Command;

use Ramsey\Uuid\Uuid;

class StartList
{
    private $id;

    private $name;

    private $hosts;

    public function __construct(Uuid $id, string $name, array $hosts)
    {
        $this->id = $id;
        $this->name = $name;
        $this->hosts = $hosts;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function hosts(): array
    {
        return $this->hosts;
    }

    public function name(): string
    {
        return $this->name;
    }
}
