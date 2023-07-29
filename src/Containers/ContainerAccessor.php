<?php

namespace SimpleFW\Containers;

class ContainerAccessor {
    private Container $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function getServiceClasses(): array {
        return $this->container->getServiceClasses();
    }
}