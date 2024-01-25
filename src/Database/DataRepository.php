<?php

namespace PowerDI\Database;

use PowerDI\Database\SQL\Column;
use PowerDI\Loaders\ComponentLoader;

abstract class DataRepository {
    protected EntityManager $entityManager;
    protected \ReflectionClass $class;

    public function __construct(EntityManager $entityManager, \ReflectionClass $class) {
        $this->class = $class;
        $this->entityManager = $entityManager;
    }

    public function get(string $query, array $arguments): mixed {
        $data = $this->getAll($query, $arguments);
        return (count($data) <= 0) ? null : $data[0];
    }

    public function getAll(string $query, array $arguments): array {
        $stmt = $this->entityManager->getConnector()->prepare($query);
        $stmt->execute($arguments);
        $data = $stmt->fetchAll();
        if (count($data) <= 0) return [];
        $instances = [];
        foreach ($data as $value) {
            $instance = $this->class->newInstanceWithoutConstructor();
            foreach ($this->class->getProperties() as $property) {
                $wasPrivate = $property->isPrivate() || $property->isProtected();
                $property->setAccessible(true);
                $columnAttributes = $property->getAttributes(Column::class);
                $name = $property->name;
                if (count($columnAttributes) > 0) {
                    $columnAttribute = $columnAttributes[0]->newInstance();
                    $name = $columnAttribute->getName();
                }

                $transientAttributes = $property->getAttributes(Transient::class);
                if (count($transientAttributes) > 0) {
                    continue;
                }

                if (ComponentLoader::hasAttribute($property, Converter::class)) {
                    $converter = ComponentLoader::instantiateAttribute($property, Converter::class);
                    $columnConverterReflection = new \ReflectionClass($converter->getClass());
                    $columnConverter = $columnConverterReflection->newInstance();
                    $property->setValue($instance, $columnConverter->dbToObject($value[$name]));
                } else {
                    $property->setValue($instance, $value[$name]);
                }

                $property->setAccessible(!$wasPrivate);
            }
            $instances[] = $instance;
        }
        return $instances;
    }

    abstract public function save($entity): void;

    abstract public function delete($entity): void;

    abstract public function deleteAll(): void;
}