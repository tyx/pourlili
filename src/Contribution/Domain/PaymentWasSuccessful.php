<?php
namespace App\Contribution\Domain;

use Prooph\EventSourcing\AggregateChanged;

class PaymentWasSuccessful extends AggregateChanged
{
    public function paymentUrl()
    {
        return $this->payload['payment_url'];
    }
}