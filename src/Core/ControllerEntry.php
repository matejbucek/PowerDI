<?php
namespace PowerDI\Core;

class ControllerEntry
{
    private $controller;
    private $controllerName;
    private $controllerClass;
    
    public function __construct($controller, $name, $class){
        $this->controller = $controller;
        $this->controllerName = $name;
        $this->controllerClass = $class;
    }
    
    public function getController(){
        return $this->controller;
    }
    
    public function getControllerName(){
        return $this->controllerName;        
    }
    
    public function getControllerClass(){
        return $this->controllerClass;
    }
}

