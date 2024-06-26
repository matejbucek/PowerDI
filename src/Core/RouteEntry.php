<?php

namespace PowerDI\Core;

class RouteEntry {
    private string $path;
    private array $methods;
    private string $controllerName;
    private string $methodName;
    private array $pathParams;
    private ?array $cacheConfig;

    /**
     * @return string
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * @return mixed
     */
    public function getPathParams() {
        return $this->pathParams;
    }

    /**
     * @param string $methodName
     */
    public function setMethodName($methodName) {
        $this->methodName = $methodName;
    }

    /**
     * @param mixed $pathParams
     */
    public function setPathParams($pathParams) {
        $this->pathParams = $pathParams;
    }

    /**
     * @return mixed
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getMethods() {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getControllerName() {
        return $this->controllerName;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * @param mixed $methods
     */
    public function setMethods($methods) {
        $this->methods = $methods;
    }

    /**
     * @param string $controllerName
     */
    public function setControllerName($controllerName) {
        $this->controllerName = $controllerName;
    }

    public function getCacheConfig() {
        return $this->cacheConfig;
    }

    public function __construct($path, $methods = [], $controllerName = NULL, $methodName = NULL, $pathParams = [], ?array $cacheConfig = null) {
        $this->path = $path;
        $this->methods = $methods;
        $this->controllerName = $controllerName;
        $this->methodName = $methodName;
        $this->pathParams = $pathParams;
        $this->cacheConfig = $cacheConfig;
    }

}

