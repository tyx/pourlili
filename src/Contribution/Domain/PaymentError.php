<?php
namespace App\Contribution\Domain;

class PaymentError extends \Exception
{
    public static function transport($previous = null)
    {
        return new static('Error during transport', 100, $previous);
    }

    public static function gateway(array $details)
    {
        return new static('Error from gateway : '.var_export($details, true));
    }
}