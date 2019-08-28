<?php
namespace App\Catalog\App\Command;

use Ramsey\Uuid\UuidInterface;

class EnableProduct
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
