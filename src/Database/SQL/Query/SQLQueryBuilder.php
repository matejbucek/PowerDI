<?php

namespace SimpleFW\Database\SQL\Query;

use SimpleFW\Database\DatabaseException;
use SimpleFW\Database\SQL\Table;
use SimpleFW\Loaders\ComponentLoader;

class SQLQueryBuilder {
    private \ReflectionClass $class;
    private Table $table;
    private ?QueryType $type;
    private array $columns;
    private array $values;
    private array $conditions;

    public function __construct(string | \ReflectionClass $class) {
        $this->class = is_string($class)? new \ReflectionClass($class) : $class;
        $this->table = ComponentLoader::instantiateAttribute($this->class, Table::class);
        $this->type = null;
        $this->columns = [];
        $this->values = [];
        $this->conditions = [];
    }

    public function select(array $columns): SQLQueryBuilder {
        if($this->type != null)
            throw new DatabaseException("Cannot change the QueryType in the middle of query");
        $this->type = QueryType::SELECT;
        $this->columns = $columns;
        return $this;
    }

    public function insert(array $values): SQLQueryBuilder {
        if($this->type != null)
            throw new DatabaseException("Cannot change the QueryType in the middle of query");
        $this->type = QueryType::INSERT;
        $this->values = $values;
        return $this;
    }

    public function update(array $values): SQLQueryBuilder {
        if($this->type != null)
            throw new DatabaseException("Cannot change the QueryType in the middle of query");
        $this->type = QueryType::UPDATE;
        $this->values = $values;
        return $this;
    }

    public function delete(): SQLQueryBuilder {
        if($this->type != null)
            throw new DatabaseException("Cannot change the QueryType in the middle of query");
        $this->type = QueryType::DELETE;
        return $this;
    }

    public function where(string $column, WhereOperators $operator, mixed $value): SQLQueryBuilder {
        $this->conditions[] = new Where($column, $operator, $value);
        return $this;
    }

    private function conditions(): Query {
        $conditions = [];
        $arguments = [];
        foreach ($this->conditions as $condition) {
            $conditions[] = $condition->toString();
            $arguments[":$condition->column"] = $condition->value;
        }
        return new Query(implode(" AND ", $conditions), $arguments);
    }

    private function buildSelect(): Query {
        $columns = implode(",", $this->columns);
        $query = "SELECT $columns FROM `{$this->table->getName()}`";
        $arguments = [];

        if(!empty($this->conditions)) {
            $conditions = $this->conditions();
            $query .= " WHERE {$conditions->query}";
            $arguments = $conditions->arguments;
        }

        $query .= ";";

        return new Query($query, $arguments);
    }

    private function buildInsert(): Query {
        $arguments = [];
        $columns = [];
        $values = [];

        foreach ($this->values as $column => $value) {
            $arguments[":$column"] = $value;
            $columns[] = $column;
            $values[] = ":$column";
        }

        $cols = "(" . implode(",", $columns) . ")";
        $vals = "(" . implode(",", $values) . ")";
        $query = "INSERT INTO `{$this->table->getName()}` $cols VALUES $vals;";
        return new Query($query, $arguments);
    }

    private function buildUpdate(): Query {
        $query = "UPDATE `{$this->table->getName()}` SET ";
        $arguments = [];
        $values = [];

        foreach ($this->values as $column => $value) {
            $arguments[":$column"] = $value;
            $values[] = "$column = :$column";
        }

        $query .= implode(",", $values);

        if(!empty($this->conditions)) {
            $conditions = $this->conditions();
            $query .= " WHERE {$conditions->query}";
            $arguments = $conditions->arguments;
        }

        $query .= ";";

        return new Query($query, $arguments);
    }

    private function buildDelete(): Query {
        $query = "DELETE FROM `{$this->table->getName()}`";
        $arguments = [];

        if(!empty($this->conditions)) {
            $conditions = $this->conditions();
            $query .= " WHERE {$conditions->query}";
            $arguments = $conditions->arguments;
        }

        $query .= ";";

        return new Query($query, $arguments);
    }

    public function build(): Query {
        return match ($this->type) {
            QueryType::SELECT => $this->buildSelect(),
            QueryType::INSERT => $this->buildInsert(),
            QueryType::UPDATE => $this->buildUpdate(),
            QueryType::DELETE => $this->buildDelete()
        };
    }
}