<?php
namespace App\Contribution\App\Query;

class AllContributionOfList
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