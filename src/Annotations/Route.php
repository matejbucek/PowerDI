<?php
namespace SimpleFW\Annotations;

use SimpleFW\HttpBasics\HttpMethod;

#[\Attribute]
class Route
{
    private $path;
    private $methods;
    private $name;
    
    public function __construct($path, $methods = [HttpMethod::GET, HttpMethod::POST], $name = NULL){
        $this->path = $path;
        $this->methods = $methods;
        $this->name = $name;
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function getMethods(){
        return $this->methods;
    }
    
    public function getName(){
        return $this->name;
    }
}

