<?php
namespace SimpleFW\Containers;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use SimpleFW\Containers\Exceptions\ServiceNotFoundException;
use SimpleFW\Containers\Exceptions\ParameterNotFoundException;
use SimpleFW\Containers\Exceptions\ContainerException;
use SimpleFW\Containers\References\ServiceReference;
use SimpleFW\Containers\References\ParameterReference;
use SimpleFW\Annotations\Autowired;

class Container implements PsrContainerInterface
{

    private $services;

    private $parameters;

    private $serviceStore;

    public function __construct(?array $services = [], ?array $parameters = [])
    {
        $this->services = $services;
        $this->parameters = $parameters;
        $this->serviceStore = [];
    }

    public function registerService($name, $class, $args = [])
    {
        $this->services[$name] = [
            "class" => $class,
            "arguments" => $args
        ];
    }

    public function registerParam($name, $value)
    {
        $tokens = explode(".", $name);
        $pos = &$this->parameters[$tokens[0]];
        array_shift($tokens);
        foreach ($tokens as $token) {
            $pos = &$pos[$token];
        }
        $pos = $value;
    }

    public function get(string $id)
    {
        if (! $this->has($id))
            throw new ServiceNotFoundException("Service with ID = $id not found.");
        if (! isset($this->serviceStore[$id])) {
            $this->serviceStore[$id] = $this->createService($id);
        }

        return $this->serviceStore[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function getParameter($name)
    {
        $tokens = explode('.', $name);
        $context = &$this->parameters;
        while (null !== ($token = array_shift($tokens))) {
            if (! isset($context[$token])) {
                throw new ParameterNotFoundException("Parameter with ID = $name not found.");
            }

            $context = &$context[$token];
        }

        return $context;
    }

    private function createService($name)
    {
        $entry = &$this->services[$name];

        if (! is_array($entry) || ! isset($entry['class'])) {
            throw new ContainerException($name . ' service entry must be an array containing a \'class\' key');
        } elseif (! class_exists($entry['class'])) {
            throw new ContainerException($name . ' service class does not exist: ' . $entry['class']);
        } elseif (isset($entry['lock'])) {
            throw new ContainerException($name . ' service contains a circular reference');
        }

        $entry['lock'] = true;

        $arguments = isset($entry['arguments']) ? $this->resolveArguments($name, $entry['arguments']) : [];

        $reflector = new \ReflectionClass($entry['class']);
        $service = $reflector->newInstanceArgs($arguments);

        $serviceReflection = new \ReflectionClass($service);
        $serviceProperties = $serviceReflection->getProperties();

        foreach ($serviceProperties as $property) {
            $attributes = $property->getAttributes(Autowired::class);
            if (!empty($attributes)) {
                $instance = $attributes[0]->newInstance();
                $resolved = $this->resolveArguments($name, [
                    $instance->getQualifier()
                ]);
                $property->setAccessible(true);
                $property->setValue($service, $resolved[0]);
                $property->setAccessible(FALSE);
            }
        }

        if (isset($entry['calls'])) {
            $this->initializeService($service, $name, $entry['calls']);
        }

        return $service;
    }

    private function resolveArguments($name, array $argumentDefinitions)
    {
        $arguments = [];

        foreach ($argumentDefinitions as $argumentDefinition) {
            if ($argumentDefinition instanceof ServiceReference) {
                $argumentServiceName = $argumentDefinition->getName();
                $arguments[] = $this->get($argumentServiceName);
            } elseif ($argumentDefinition instanceof ParameterReference) {
                $argumentParameterName = $argumentDefinition->getName();
                $arguments[] = $this->getParameter($argumentParameterName);
            } else {
                if (preg_match("/%[\w|.]*%/", $argumentDefinition)) {
                    $arguments[] = $this->getParameter(str_replace("%", "", $argumentDefinition));
                } else if (preg_match("/@[\w|.]*/", $argumentDefinition)) {
                    $arguments[] = $this->get(str_replace("@", "", $argumentDefinition));
                } else {
                    $arguments[] = $argumentDefinition;
                }
            }
        }
        return $arguments;
    }

    private function initializeService($service, $name, array $callDefinitions)
    {
        foreach ($callDefinitions as $callDefinition) {
            if (! is_array($callDefinition) || ! isset($callDefinition['method'])) {
                throw new ContainerException($name . ' service calls must be arrays containing a \'method\' key');
            } elseif (! is_callable([
                $service,
                $callDefinition['method']
            ])) {
                throw new ContainerException($name . ' service asks for call to uncallable method: ' . $callDefinition['method']);
            }

            $arguments = isset($callDefinition['arguments']) ? $this->resolveArguments($name, $callDefinition['arguments']) : [];

            call_user_func_array([
                $service,
                $callDefinition['method']
            ], $arguments);
        }
    }
}

