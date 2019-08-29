<?php
declare(strict_types=1);

namespace App\Contribution\App\Query;

class GuestBookOfList
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
