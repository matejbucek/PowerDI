<?php

namespace SimpleFW\Database\SQL;

use SimpleFW\Database\DatabaseException;
use SimpleFW\Database\DataRepository;
use SimpleFW\Database\Entity;
use SimpleFW\Database\EntityManager;
use SimpleFW\Database\Helpers;
use SimpleFW\Database\SQL\Query\SQLQueryBuilder;
use SimpleFW\Database\SQL\Query\WhereOperators;
use SimpleFW\Database\Transient;
use SimpleFW\Loaders\ComponentLoader;

abstract class SqlRepository extends DataRepository {
    protected Table $table;
    protected string $id;

    public function __construct(EntityManager $entityManager, \ReflectionClass $class) {
        parent::__construct($entityManager, $class);
        $tableAttribute = $this->class->getAttributes(Table::class);
        if (count($tableAttribute) <= 0) throw new DatabaseException();
        $this->table = $tableAttribute[0]->newInstance();

        $idProperty = ComponentLoader::filterProperties($class->getName(), ID::class);
        if (count($idProperty) <= 0) throw new DatabaseException();
        $this->id = $idProperty[0]->name;

        $idColumn = $idProperty[0]->getAttributes(Column::class);
        if (count($idColumn) > 0) {
            $this->id = $idColumn[0]->newInstance()->getName();
        }
    }

    public function findAll(): array {
        $tableName = $this->table->getName();
        return $this->getAll("SELECT * FROM $tableName;", []);
    }

    public function find($id): mixed {
        $tableName = $this->table->getName();
        return $this->get("SELECT * FROM `$tableName` WHERE $this->id = :id_value;", [":id_value" => $id]);
    }

    public function save($entity): void {
        if (!$this->class->isInstance($entity))
            throw new DatabaseException("Entity is not an instance of class {$this->class->name}");

        $propertyMappings = [];

        //Ignoring ID and Transient properties
        foreach ($this->class->getProperties() as $property) {
            if (ComponentLoader::hasAttribute($property, ID::class)
                || ComponentLoader::hasAttribute($property, Transient::class))
                continue;
            $propertyMappings[Helpers::columnNameOr($property, $property->name)] = $property->getValue($entity);
        }

        $idValue = $this->class->getProperty($this->id)->getValue($entity);

        if ($idValue == null) {
            $query = (new SQLQueryBuilder($this->class))->insert($propertyMappings)->build();
        } else {
            $query = (new SQLQueryBuilder($this->class))->update($propertyMappings)->where($this->id, WhereOperators::Equal, $idValue)->build();
        }

        print_r($query);

        $stmt = $this->entityManager->getConnector()->prepare($query->query);
        $stmt->execute($query->arguments);
    }

    public function delete($entity): void {
        $idValue = $this->class->getProperty($this->id)->getValue($entity);
        $query = (new SQLQueryBuilder($this->class))->delete()->where($this->id, WhereOperators::Equal, $idValue)->build();
        $stmt = $this->entityManager->getConnector()->prepare($query->query);
        $stmt->execute($query->arguments);
    }

    public function deleteAll(): void {
        $query = (new SQLQueryBuilder($this->class))->delete()->build();
        $stmt = $this->entityManager->getConnector()->prepare($query->query);
        $stmt->execute($query->arguments);
    }
}