<?php
declare(strict_types=1);

namespace App\Catalog\App\Command;

use Ramsey\Uuid\UuidInterface;

class UploadImageOfProduct
{
    private $id;

    private $imagePath;

    public function __construct(UuidInterface $id, string $imagePath)
    {
        $this->id = $id;
        $this->imagePath = $imagePath;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function imagePath(): string
    {
        return $this->imagePath;
    }
}
