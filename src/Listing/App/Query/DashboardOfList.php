<?php
declare(strict_types=1);

namespace App\Listing\App\Query;

class DashboardOfList
{
    private $listId;

    public function __construct(string $listId)
    {
        $this->listId = $listId;
    }

    public function listId(): string
    {
        return $this->listId;
    }
}
