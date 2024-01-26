<?php

namespace PowerDI\Plugins\Builtin\Debugger;

use PowerDI\Core\Autowired;
use PowerDI\Core\ContainerAccessor;
use PowerDI\Core\References\ServiceReference;
use PowerDI\Core\Router;
use PowerDI\Loaders\ComponentLoader;
use PowerDI\Plugins\AbstractPlugin;
use PowerDI\Plugins\Plugin;

#[Plugin]
class Debugger extends AbstractPlugin {
    #[Autowired("@ContainerAccessor")]
    private ContainerAccessor $containerAccessor;
    #[Autowired("@Router")]
    private Router $router;

    function selfInstall(): void {
        $this->registerEndpoints();
    }
    protected function registerEndpoints(): void {
        //FIXME: Use plugin context PathResolver!
        $controllerName = ComponentLoader::resolveServiceName(DebuggerController::class);
        $this->containerAccessor->registerService($controllerName, DebuggerController::class, [new ServiceReference("AbstractTemplater"), new ServiceReference("PathResolver")]);
        $this->router->getRouteRegistry()->registerController(DebuggerController::class, $controllerName, $this->containerAccessor->getService($controllerName));

        $apiControllerName = ComponentLoader::resolveServiceName(DebuggerApiController::class);
        $this->containerAccessor->registerService($apiControllerName, DebuggerApiController::class, [new ServiceReference("AbstractTemplater"), new ServiceReference("PathResolver")]);
        $this->router->getRouteRegistry()->registerController(DebuggerApiController::class, $apiControllerName, $this->containerAccessor->getService($apiControllerName));
    }

}