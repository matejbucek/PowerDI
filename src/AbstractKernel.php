<?php

namespace PowerDI;

use PowerDI\Core\Container;
use PowerDI\Core\ContainerAccessor;
use PowerDI\Core\Router;
use PowerDI\Loaders\ComponentLoader;
use PowerDI\Loaders\PathResolver;
use PowerDI\HttpBasics\HttpResponse;
use PowerDI\HttpBasics\HttpRequest;
use PowerDI\Core\RouteRegistry;
use PowerDI\Logging\VoidLogger;
use PowerDI\Templates\LatteTemplater;
use PowerDI\Core\References\ServiceReference;
use PowerDI\Core\SessionContext;
use PowerDI\Security\Firewall;
use PowerDI\HttpBasics\Exceptions\AccessForbiddenException;

abstract class AbstractKernel {
    protected string $appBase;
    protected Container $container;
    protected array $config;
    protected array $dependency;
    protected array $routes;
    protected RouteRegistry $routeRegistry;
    protected array $firewallConfig;
    protected string $envFilePath;
    protected array $envVariables;

    public function __construct() {
        $this->setAppBase();
        $this->configure();
        $this->loadEnvVariables();
        $this->emplaceEnvVariables();
        $this->container = new Container($this->dependency["services"], $this->dependency["parameters"]);
        $cacheService = null;
        if (isset($this->config["app"]["cache"]["service"]) && $this->container->has($this->config["app"]["cache"]["service"])) {
            $cacheService = $this->container->get($this->config["app"]["cache"]["service"]);
        }
        $this->routeRegistry = new RouteRegistry($cacheService);
        $this->registerDefaults();
        $this->prepareClasses();
        try {
            $this->container->checkForInstantiation();
        } catch (\Exception $exception) {
            $this->container->get("Logger")->error("Exception thrown in CheckForInstantiation phase: {$exception->getMessage()}");
            die("Check the log");
        }
        $this->loadClasses($this->container->getParameter("app.user.classes"));
    }

    protected function registerDefaults() {
        $this->container->registerParam("app.base", $this->appBase);
        $this->container->registerParam("pathresolver.paths", ["templates" => "templates/"]);
        $this->container->registerService("PathResolver", PathResolver::class, ["%app.base%", "%pathresolver.paths%"]);
        $this->container->registerService("AbstractTemplater", LatteTemplater::class, ["%pathresolver.paths%"]);
        $this->container->registerService("SessionContext", SessionContext::class);
        $this->container->registerService("ContainerAccessor", ContainerAccessor::class, [$this->container]);
        $this->container->registerService("Router", Router::class, [$this->routeRegistry]);
        if ($this->firewallConfig["firewall"]["status"] == "on") {
            $this->container->registerParam("FirewallConfig", $this->firewallConfig);
            $this->container->registerService("Firewall", Firewall::class, [$this->firewallConfig["firewall"]["user"]["binder"], "%FirewallConfig%"]);
        }
        if (!$this->container->has("Logger"))
            $this->container->registerService("Logger", VoidLogger::class, []);
    }

    protected abstract function setAppBase();

    protected abstract function configure();

    private function prepareClasses() {
        $prefix = $this->config["app"]["lookup"]["prefix"];
        $paths = $this->config["app"]["lookup"]["paths"];
        $fullPaths = [];
        foreach ($paths as $path) {
            $fullPaths[] = $this->appBase . "/" . $prefix . $path;
        }
        $files = ComponentLoader::recursiveScan($fullPaths);
        foreach ($files as &$file) {
            $file = str_replace($this->appBase . "/" . $prefix, "App\\", $file);
            $file = str_replace("/", "\\", $file);
            $file = str_replace(".php", "", $file);
        }
        $this->container->registerParam("app.user.classes", $files);
    }

    private function loadClasses($files): void {
        $controllers = ComponentLoader::filterControllers($files);
        $services = ComponentLoader::filterServices($files);

        foreach ($services as $service) {
            $name = ComponentLoader::resolveServiceName($service);
            $args = ComponentLoader::resolveServiceArgs($service);
            $this->container->registerService($name, $service, $args);
        }

        foreach ($controllers as $controller) {
            $name = ComponentLoader::resolveServiceName($controller);
            $this->container->registerService($name, $controller, [new ServiceReference("AbstractTemplater"), new ServiceReference("PathResolver")]);
            $this->routeRegistry->registerController($controller, $name, $this->container->get($name));
        }
    }

    public function handle(HttpRequest $request): HttpResponse {
        $this->container->registerParam("app.request", $request);
        try {
            if ($this->firewallConfig["firewall"]["status"] == "on") {
                $firewall = $this->container->get("Firewall");
                if (!$firewall->canAccess($request))
                    throw new AccessForbiddenException();
            }
            return $this->routeRegistry->resolve($request);
        } catch (\Exception $exception) {
            $controller = $this->container->get($this->config["app"]["errors"]["name"]);
            $reflectionMethod = new \ReflectionMethod(get_class($controller), $this->config["app"]["errors"]["method"]);
            return $reflectionMethod->invoke($controller, $exception, $request);
        }
    }

    public function schedule(): void {
        $scheduler = $this->container->get("Scheduler");
        $scheduler->schedule();
    }

    private function loadEnvVariables(): void {
        $variables = [];
        if (!file_exists($this->envFilePath)) {
            return;
        }

        $envFile = file($this->envFilePath);
        if ($envFile) {
            foreach ($envFile as $line) {
                [$key, $value] = explode("=", $line, 2);
                $key = trim($key);
                $value = trim($value);

                $variables[$key] = $value;
            }
        }
        $this->envVariables = $variables;
    }

    private function emplaceEnvVariables(): void {
        $this->emplaceEnvVariable($this->dependency["parameters"]);
    }

    private function emplaceEnvVariable(&$node): void {
        foreach ($node as $key => &$value) {
            if (is_array($value)) {
                $this->emplaceEnvVariable($value);
            } elseif (is_string($value)) {
                $matches = [];
                if (preg_match_all("/\{[a-zA-Z_]+[a-zA-Z0-9_]*}/", $value, $matches)) {
                    foreach ($matches[0] as $match) {
                        $variable = preg_replace("/[\{\}]/", "", $match);
                        $variableValue = array_key_exists($variable, $this->envVariables) ? $this->envVariables[$variable] : "";
                        $value = preg_replace("/\{$variable\}/", $variableValue, $value);
                    }
                }
            }
        }
    }
}