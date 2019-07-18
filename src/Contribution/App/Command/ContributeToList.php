<?php
namespace App\Contribution\App\Command;

class ContributeToList
{
    private $contributionId;

    private $listId;

    private $basketId;

    private $amount;

    private $email;

    private $signature;

    private $message;

    public function __construct($contributionId, string $listId, string $basketId, float $amount, string $email, string $signature, ?string $message = null)
    {
        $this->contributionId = $contributionId;
        $this->listId = $listId;
        $this->basketId = $basketId;
        $this->amount = $amount;
        $this->email = $email;
        $this->signature = $signature;
        $this->message = $message;
    }

    public function contributionId()
    {
        return $this->contributionId;
    }

    public function listId(): string
    {
        return $this->listId;
    }

    public function basketId(): string
    {
        return $this->basketId;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function signature(): string
    {
        return $this->signature;
    }

    public function message(): ?string
    {
        return $this->message;
    }

}