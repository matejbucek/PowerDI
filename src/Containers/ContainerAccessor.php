<?php

namespace SimpleFW\Containers;

class ContainerAccessor {
    private Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function registerService(string $name, string $class, array $arguments): void {
        $this->container->registerService($name, $class, $arguments);
    }

    public function getServiceClasses(): array {
        return $this->container->getServiceClasses();
    }

    public function getService(string $id) {
        return $this->container->get($id);
    }
}