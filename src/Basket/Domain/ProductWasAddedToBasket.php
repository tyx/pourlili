<?php 
namespace App\Basket\Domain;

use Prooph\EventSourcing\AggregateChanged;

class ProductWasAddedToBasket extends AggregateChanged
{
    public function productId()
    {
        return $this->payload['product_id'];
    }

    public function amountOfContribution()
    {
        return $this->payload['amount_contribution'];
    }
}