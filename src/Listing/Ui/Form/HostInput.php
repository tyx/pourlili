<?php
namespace App\Listing\Ui\Form;

class HostInput
{
    public $name;

    public $enabled;

    public function __construct($name)
    {
        $this->name = $name;
    }
}
