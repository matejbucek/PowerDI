<?php
namespace PowerDI\Security;

class SimpleRole implements Role
{
    private $name;
    public function getName(): string
    {
        return $this->name;
    }
    
    public function __construct($name){
        $this->name = $name;
    }
}

