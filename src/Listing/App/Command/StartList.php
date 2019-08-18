<?php
namespace App\Listing\App\Command;

use Ramsey\Uuid\Uuid;

class StartList
{
    private $id;

    private $host;

    public function __construct(Uuid $id, string $host)
    {
        $this->id = $id;
        $this->host = $host;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function host(): string
    {
        return $this->host;
    }
}