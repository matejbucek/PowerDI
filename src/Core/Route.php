<?php
namespace PowerDI\Core;

use PowerDI\HttpBasics\HttpMethod;

#[\Attribute]
class Route
{
    private string $path;
    private array $methods;
    private ?array $cacheConfig;
    private ?string $name;

    
    public function __construct(string $path, array $methods = [HttpMethod::GET, HttpMethod::POST], ?array $cacheConfig = null, ?string $name = null){
        $this->path = $path;
        $this->methods = $methods;
        $this->cacheConfig = $cacheConfig;
        $this->name = $name;
    }
    
    public function getPath(){
        return $this->path;
    }
    
    public function getMethods(){
        return $this->methods;
    }

    public function getCacheConfig(){
        return $this->cacheConfig;
    }
    
    public function getName(){
        return $this->name;
    }
}

