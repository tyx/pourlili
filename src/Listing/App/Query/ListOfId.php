<?php
namespace App\Listing\App\Query;

use Ramsey\Uuid\UuidInterface;

class ListOfId
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