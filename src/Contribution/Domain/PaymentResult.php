<?php
namespace App\Contribution\Domain;

class PaymentResult
{
    private $paymentUrl;

    private $succeeded;

    private $details;

    public function __construct(string $paymentUrl, bool $succeeded, array $details)
    {
        $this->paymentUrl = $paymentUrl;
        $this->succeeded = $succeeded;
        $this->details = $details;
    }

    public function paymentUrl(): string
    {
        return $this->paymentUrl;
    }

    public function succeeded(): bool
    {
        return $this->succeeded;
    }

    public function details(): array
    {
        return $this->details;
    }
}