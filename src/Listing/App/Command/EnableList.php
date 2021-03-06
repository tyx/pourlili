<?php
namespace App\Listing\App\Command;

use Ramsey\Uuid\UuidInterface;

class EnableList
{
    private $id;

    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }
}