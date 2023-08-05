<?php

namespace SimpleFW\Database;

use SimpleFW\Annotations\Autowired;
use SimpleFW\Annotations\Init;
use SimpleFW\Containers\ContainerAccessor;
use SimpleFW\Loaders\ComponentLoader;
use SimpleFW\Logging\Logger;

class EntityManager {

    #[Autowired("%app.user.classes%")]
    private array $classes;

    #[Autowired("@ContainerAccessor")]
    private ContainerAccessor $containerAccessor;
    private Connector $connector;
    private array $entities;

    public function __construct(Connector $connector) {
        $this->connector = $connector;
    }

    #[Init]
    private function init(): void {
        $this->loadCompatibleRepositories();
    }

    private function loadCompatibleRepositories() {
        $repos = ComponentLoader::filter($this->classes, Repository::class);
        foreach ($repos as $repo) {
            $class = new \ReflectionClass($repo);
            $attribute = $class->getAttributes(Repository::class)[0]->newInstance();
            if($attribute->getType() == $this->connector->getType()){
                $this->containerAccessor->registerService(ComponentLoader::resolveServiceName($class->getName()), $class->getName(), [$this, new \ReflectionClass($attribute->getClass())]);
            }
        }
    }

    public function getConnector(): Connector {
        return $this->connector;
    }
}