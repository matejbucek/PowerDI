<?php
namespace SimpleFW\HttpBasics;

class HttpRequest {
    private $path;
    private $method;
    private $params;
    
    public function __construct($path, $method, $params = NULL){
        $this->path = $path;
        $this->method = $method;
        $this->params = $params;
    }
    
    public static function createFromGlobas() : HttpRequest{
        return new HttpRequest(strtok($_SERVER["REQUEST_URI"], '?'), $_SERVER['REQUEST_METHOD'], $_REQUEST);
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getParams() {
        return $this->params;
    }

    public function getParam(string $name): string {
        return$this->params[$name];
    }
}

