<?php
namespace PowerDI\Core;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Service
{
    private $name;
    private $args;
    
    public function __construct(?string $name = null, array $args = []){
        $this->name = $name;
        $this->args = $args;
    }
    
    public function getName() : ?string{
        return $this->name;
    }
    
    public function getArgs() : array{
        return $this->args;
    }
}

