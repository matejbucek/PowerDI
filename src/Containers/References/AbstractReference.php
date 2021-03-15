<?php
namespace SimpleFW\Containers\References;

abstract class AbstractReference
{
    private $name;
    
    public function __construct($name){
        $this->name = $name;
    }
    
    public function getName(){
        return $this->name;
    }
}

