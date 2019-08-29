<?php
namespace App\Listing\App\Command;

use Ramsey\Uuid\UuidInterface;

class StartList
{
    private $id;

    private $host;

    private $originId;

    public function __construct(UuidInterface $id, string $host, ?UuidInterface $originId = null)
    {
        $this->id = $id;
        $this->host = $host;
        $this->originId = $originId;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function originId(): ?UuidInterface
    {
        return $this->originId;
    }
}
