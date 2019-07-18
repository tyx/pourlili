<?php
namespace App\Basket\Domain;

interface CatalogService
{
    public function productOfList($listId, $productId);
}