<?php
namespace PowerDI\HttpBasics;

use PowerDI\Core\CoreException;

class HttpRequest {
    private $path;
    private HttpMethod $method;
    private $params;
    private ?array $pathVariables;
    private ?array $headers;
    private ?array $files;

    public function __construct(?string $path, ?string $method, mixed $params = null, ?array $headers = null, ?array $files){
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

    public function getJsonBody() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function paramsToObject(string $className) {
        $class = new \ReflectionClass($className);

        try {
            $object = $class->newInstance();
        } catch (\Exception $exception) {
            throw new CoreException("When mapping parameters to an object, the object should have a constructor that accepts no parameters too!");
        }

        foreach ($this->params as $param => $value) {
            if($class->hasProperty($param)) {
                $property = $class->getProperty($param);
                try {
                    $property->setValue($object, $value);
                } catch (\Exception $exception) {
                    throw new CoreException("The parameter value has unacceptable type!");
                }
            }
        }

        return $object;
    }
}

