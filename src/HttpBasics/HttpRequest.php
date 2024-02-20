<?php
namespace PowerDI\HttpBasics;

class HttpRequest {
    private $path;
    private HttpMethod $method;
    private $params;
    private ?array $pathVariables;
    private ?array $headers;
    private ?array $files;

    public function __construct($path, string $method, $params = NULL, array $headers = null, array $files){
        $this->path = $path;
        $this->method = HttpMethod::tryFrom($method);
        $this->params = $params;
        $this->headers = $headers;
        $this->files = $files;
    }

    public static function createFromGlobas() : HttpRequest{
        return new HttpRequest(strtok($_SERVER["REQUEST_URI"], '?'), $_SERVER['REQUEST_METHOD'], $_REQUEST, getallheaders(), File::createFromGlobals());
    }

    public function getPath() {
        return $this->path;
    }
    
    public function getMethod(): HttpMethod {
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

    public function getPathVariable(string $name) {
        return ($this->pathVariables[$name] == "null")? null : $this->pathVariables[$name];
    }

    public function getHeaders(): ?array {
        return $this->headers;
    }

    public function getHeader(string $name): mixed {
        return $this->headers[$name];
    }

    public function getFile(string $name): ?File {
        return ($this->files[$name] == "null")? null : $this->files[$name];
    }

    public function getFiles(): array {
        return $this->files;
    }
}

