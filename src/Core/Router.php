<?php

namespace PowerDI\Core;

class Router {
    private RouteRegistry $routeRegistry;

    public function __construct(RouteRegistry $routeRegistry) {
        $this->routeRegistry = $routeRegistry;
    }

    public function getRouteRegistry(): RouteRegistry {
        return $this->routeRegistry;
    }
}