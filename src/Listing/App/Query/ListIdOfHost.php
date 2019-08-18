<?php
namespace App\Listing\App\Query;

class ListIdOfHost
{
    private $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }

    public function host(): string
    {
        return $this->host;
    }
}