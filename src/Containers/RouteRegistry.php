<?php
namespace SimpleFW\Containers;

use SimpleFW\Annotations\Route;
use SimpleFW\HttpBasics\Exceptions\PageNotFoundException;
use SimpleFW\HttpBasics\Exceptions\MethodNotSupportedException;

class RouteRegistry
{
    private $entries;
    private $controllers;
    
    public function __construct(){
        $this->entries = [];
        $this->controllers = [];
    }
    
    public function registerController($class, $name, $controller){
        $this->controllers[$name] = new ControllerEntry($controller, $name, $class);
        $reflectionClass = new \ReflectionClass($class);
        $controllerBase = $reflectionClass->getAttributes(Route::class);
        $base = "";
        if(count($controllerBase) == 1){
            $cRoute = $controllerBase[0]->newInstance();
            $base = $cRoute->getPath();
        }
        
        foreach ($reflectionClass->getMethods() as $method){
            $mAttribute = $method->getAttributes(Route::class);
            if(count($mAttribute) == 1){
                $mRoute = $mAttribute[0]->newInstance();
                $this->entries[] = new RouteEntry($base . $mRoute->getPath(), $mRoute->getMethods(), $name, $method->getName());
            }
        }
    }
    
    public function resolve($request){
        $entry = $this->findMatchingEntry($request);
        $reflectionMethod = new \ReflectionMethod($this->controllers[$entry->getControllerName()]->getControllerClass(), $entry->getMethodName());
        return $reflectionMethod->invoke($this->controllers[$entry->getControllerName()]->getController(), $request);
    }
    
    private function findMatchingEntry($request): RouteEntry {
        foreach ($this->entries as $entry) {
            if($this->prepareUrl($entry->getPath()) == $this->prepareUrl($request->getPath())){
                if(in_array($request->getMethod(), $entry->getMethods())){
                    return $entry;
                }
            } else {
                $entryUrl = explode("/", $this->prepareUrl($entry->getPath()));
                $requestUrl = explode("/", $this->prepareUrl($request->getPath()));
                $matches = true;

                $pathVariables = [];

                if(count($entryUrl) != count($requestUrl)) continue;
                for ($i = 0; $i < count($entryUrl); $i++) {
                    if(preg_match("/^\{\w*\}$/", $entryUrl[$i])) {
                        $pathVariables[substr($entryUrl[$i], 1, -1)] = $requestUrl[$i];
                    } else if($entryUrl[$i] != $requestUrl[$i]){
                        $matches = false;
                        break;
                    }
                }

                if($matches && in_array($request->getMethod(), $entry->getMethods())) {
                    $request->setPathVariables($pathVariables);
                    return $entry;
                }
            }
        }
        throw new PageNotFoundException();        
    }
    
    private function prepareUrl($url) {
        $url_split = str_split($url);
        if(end($url_split) != "/"){
            return ($url."/");
        }
        return $url;
    }
}

