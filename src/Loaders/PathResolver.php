<?php
namespace SimpleFW\Loaders;

class PathResolver
{
    private $base;
    private $paths;
    
    public function __construct($base, $paths = []){
        $this->base = $base;
        $this->paths = $paths;
    }
    
    public function resolve($path){
        return $this->base."/".$path;
    }
    
    public function resolveTemplate($path){
        return $this->base."/".$this->paths["templates"].$path;
    }
}

