<?php
namespace App\Contribution\App\Query;

use Ramsey\Uuid\Uuid;

class PaymentUrlOfContribution
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