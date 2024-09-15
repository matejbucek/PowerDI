<?php

namespace PowerDI\Database\SQL;

use PowerDI\Database\Converter;
use PowerDI\Database\DatabaseException;
use PowerDI\Database\DataRepository;
use PowerDI\Database\Entity;
use PowerDI\Database\EntityManager;
use PowerDI\Database\Helpers;
use PowerDI\Database\MySQLConnector;
use PowerDI\Database\SQL\Query\SQLQueryBuilder;
use PowerDI\Database\SQL\Query\WhereOperators;
use PowerDI\Database\Transient;
use PowerDI\Loaders\ComponentLoader;
use ReflectionException;

/**
 * SQLRepository is a wrapper of DataRepository, that enables
 * PowerDI to use SQL specific functionalities.
 */
abstract class SqlRepository extends DataRepository {
    /**
     * Instance of a Table annotation from the Entity
     * @var Table
     */
    protected Table $table;
    /**
     * The name of the property, that is used as an identifier in the DB.
     * @var string
     */
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

    public function beginTransaction() {
        $this->entityManager->getConnector()->begin();
    }

    public function commitTransaction() {
        $this->entityManager->getConnector()->commit();
    }

    public function rollbackTransaction() {
        $this->entityManager->getConnector()->rollBack();
    }

    /**
     * Returns all entities from the database table.
     * @return array
     */
    public function findAll(): array {
        $tableName = $this->table->getName();
        return $this->getAll("SELECT * FROM `$tableName`;", []);
    }

    /**
     * Finds an entity by its ID.
     * @param $id
     * @return mixed
     */
    public function find($id): mixed {
        $tableName = $this->table->getName();
        return $this->get("SELECT * FROM `$tableName` WHERE $this->id = :id_value;", [":id_value" => $id]);
    }

    /**
     * Creates or updates an entity in the database.
     * @param $entity
     * @return void
     * @throws DatabaseException
     * @throws ReflectionException
     */
    public function save($entity): void {
        if (!$this->class->isInstance($entity))
            throw new DatabaseException("Entity is not an instance of class {$this->class->name}");

        $propertyMappings = [];

        //Ignoring ID and Transient properties
        foreach ($this->class->getProperties() as $property) {
            if (ComponentLoader::hasAttribute($property, ID::class)
                || ComponentLoader::hasAttribute($property, Transient::class))
                continue;

            if (ComponentLoader::hasAttribute($property, Converter::class)) {
                $converter = ComponentLoader::instantiateAttribute($property, Converter::class);
                $columnConverterReflection = new \ReflectionClass($converter->getClass());
                $columnConverter = $columnConverterReflection->newInstance();
                $propertyMappings[Helpers::columnNameOr($property, $property->name)] = $columnConverter->objectToDB($property->getValue($entity));
            } else {
                $propertyMappings[Helpers::columnNameOr($property, $property->name)] = $property->getValue($entity);
            }
        }

        $idValue = $this->class->getProperty($this->id)->getValue($entity);

        if ($idValue == null) {
            $query = (new SQLQueryBuilder($this->class))->insert($propertyMappings)->build();
        } else {
            $query = (new SQLQueryBuilder($this->class))->update($propertyMappings)->where($this->id, WhereOperators::Equal, $idValue)->build();
        }

        $stmt = $this->entityManager->getConnector()->prepare($query->query);
        $stmt->execute($query->arguments);
    }

    /**
     * Deletes an entity from the database.
     * @param $entity
     * @return void
     * @throws DatabaseException|ReflectionException
     */
    public function delete($entity): void {
        $idValue = $this->class->getProperty($this->id)->getValue($entity);
        $query = (new SQLQueryBuilder($this->class))->delete()->where($this->id, WhereOperators::Equal, $idValue)->build();
        $stmt = $this->entityManager->getConnector()->prepare($query->query);
        $stmt->execute($query->arguments);
    }

    /**
     * Deletes all entities from the database table.
     * @return void
     * @throws DatabaseException
     */
    public function deleteAll(): void {
        $query = (new SQLQueryBuilder($this->class))->delete()->build();
        $stmt = $this->entityManager->getConnector()->prepare($query->query);
        $stmt->execute($query->arguments);
    }
}