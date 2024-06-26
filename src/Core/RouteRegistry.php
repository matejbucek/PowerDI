<?php

namespace PowerDI\Core;

use PowerDI\Cache\CacheService;
use PowerDI\HttpBasics\Exceptions\PageNotFoundException;
use PowerDI\HttpBasics\HttpMethod;
use PowerDI\HttpBasics\HttpRequest;

class RouteRegistry {
    private array $entries;
    private array $controllers;
    private ?CacheService $cacheService;

    public function __construct(?CacheService $cacheService = null) {
        $this->entries = [];
        $this->controllers = [];
        $this->cacheService = $cacheService;
    }

    public function registerController($class, $name, $controller) {
        $this->controllers[$name] = new ControllerEntry($controller, $name, $class);
        $reflectionClass = new \ReflectionClass($class);
        $controllerBase = $reflectionClass->getAttributes(Route::class);
        $base = "";
        if (count($controllerBase) == 1) {
            $cRoute = $controllerBase[0]->newInstance();
            $base = $cRoute->getPath();
        }

        foreach ($reflectionClass->getMethods() as $method) {
            $mAttribute = $method->getAttributes(Route::class);
            if (count($mAttribute) == 1) {
                $mRoute = $mAttribute[0]->newInstance();
                $this->entries[] = new RouteEntry($base . $mRoute->getPath(), $mRoute->getMethods(), $name, $method->getName(), [], $mRoute->getCacheConfig());
            }
        }
    }

    private function resolveAutoCacheable(HttpRequest $request, RouteEntry $entry, \ReflectionMethod $reflectionMethod, array $cacheConfig) {
        $cacheConfig["key"] = $entry->getControllerName() . $entry->getMethodName() . $request->getPath();
        return $this->resolveManuallyCacheable($request, $entry, $reflectionMethod, $cacheConfig);
    }

    private function resolveManuallyCacheable(HttpRequest $request, RouteEntry $entry, \ReflectionMethod $reflectionMethod, array $cacheConfig) {
        $cacheKey = $cacheConfig["key"];
        $cached = $this->cacheService->get($cacheKey);
        if ($cached != null) {
            return $cached;
        }
        $response = $reflectionMethod->invoke($this->controllers[$entry->getControllerName()]->getController(), $request);
        $this->cacheService->set($cacheKey, $response, $cacheConfig["ttl"] ?: 0);
        return $response;
    }

    public function resolve(HttpRequest $request) {
        $entry = $this->findMatchingEntry($request);
        $reflectionMethod = new \ReflectionMethod($this->controllers[$entry->getControllerName()]->getControllerClass(), $entry->getMethodName());
        $cacheConfig = $entry->getCacheConfig();
        if ($cacheConfig != null && $this->cacheService != null) {
            return match ($cacheConfig["type"]) {
                CacheType::AutoCacheable => $this->resolveAutoCacheable($request, $entry, $reflectionMethod, $cacheConfig),
                CacheType::ManuallyCacheable => $this->resolveManuallyCacheable($request, $entry, $reflectionMethod, $cacheConfig),
                CacheType::PerUserCacheable => $this->resolvePerUserCacheable($request, $entry, $reflectionMethod, $cacheConfig),
                default => $reflectionMethod->invoke($this->controllers[$entry->getControllerName()]->getController(), $request)
            };
        }

        return $reflectionMethod->invoke($this->controllers[$entry->getControllerName()]->getController(), $request);
    }

    private function findMatchingEntry(HttpRequest $request): RouteEntry {
        foreach ($this->entries as $entry) {
            if ($this->prepareUrl($entry->getPath()) == $this->prepareUrl($request->getPath())) {
                if (in_array($request->getMethod(), $entry->getMethods())) {
                    return $entry;
                }
            } else {
                $entryUrl = array_values(array_filter(explode("/", $this->prepareUrl($entry->getPath()))));
                $requestUrl = array_values(array_filter(explode("/", $this->prepareUrl($request->getPath()))));

                if (count($entryUrl) != count($requestUrl)) continue;

                $pathVariables = [];
                $matches = self::pathMatches($entryUrl, $requestUrl, $pathVariables);

                if ($matches && in_array($request->getMethod(), $entry->getMethods())) {
                    $request->setPathVariables($pathVariables);
                    return $entry;
                }
            }
        }
        throw new PageNotFoundException();
    }

    public static function pathMatches(array $entryUrl, array $requestUrl, array &$pathVariables): bool {
        $matches = true;
        for ($i = 0; $i < count($entryUrl); $i++) {
            if (preg_match("/^\{\w*\}$/", $entryUrl[$i])) {
                $pathVariables[substr($entryUrl[$i], 1, -1)] = $requestUrl[$i];
            } else if (preg_match("/^\*\*$/", $entryUrl[$i])) {
                continue;
            } else if (!isset($requestUrl[$i])) {
                $matches = false;
                break;
            } else if ($entryUrl[$i] != $requestUrl[$i]) {
                $matches = false;
                break;
            }
        }
        return $matches;
    }

    public static function prepareUrl($url) {
        $url_split = str_split($url);
        if (end($url_split) != "/") {
            return ($url . "/");
        }
        return $url;
    }
}

