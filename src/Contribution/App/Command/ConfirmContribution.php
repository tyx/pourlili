<?php
namespace App\Contribution\App\Command;

use Ramsey\Uuid\Uuid;

class ConfirmContribution
{
    private $contributionId;

    public function __construct(Uuid $contributionId)
    {
        $this->contributionId = $contributionId;
    }

    public function contributionId(): Uuid
    {
        return $this->contributionId;
    }
}