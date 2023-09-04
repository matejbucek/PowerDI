<?php
namespace PowerDI\HttpBasics;

class HttpRequest {
    private $path;
    private $method;
    private $params;
    private ?array $pathVariables;

    private ?array $headers;

    public function __construct($path, $method, $params = NULL, array $headers = null){
        $this->path = $path;
        $this->method = $method;
        $this->params = $params;
        $this->headers = $headers;
    }

    public static function createFromGlobas() : HttpRequest{
        return new HttpRequest(strtok($_SERVER["REQUEST_URI"], '?'), $_SERVER['REQUEST_METHOD'], $_REQUEST, getallheaders());
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

    public function getParam(string $name) {
        return ($this->params[$name] == "null")? null : $this->params[$name];
    }

    public function setPathVariables(array $pathVariables): void {
        $this->pathVariables = $pathVariables;
    }

    public function getPathVariables(): ?array {
        return $this->pathVariables;
    }

    public function getHeaders(): ?array {
        return $this->headers;
    }

    public function getHeader(string $name): mixed {
        return $this->headers[$name];
    }
}

