<?php
declare(strict_types=1);

namespace App\Listing\App\Command;

use Ramsey\Uuid\UuidInterface;

class SortProducts
{
    private $id;

    private $sortedProductIds;

    public function __construct(UuidInterface $id, array $sortedProductIds)
    {
        $this->id = $id;
        $this->sortedProductIds = $sortedProductIds;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function sortedProductIds(): array
    {
        return $this->sortedProductIds;
    }
}
