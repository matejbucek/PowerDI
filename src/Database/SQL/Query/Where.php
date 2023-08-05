<?php

namespace SimpleFW\Database\SQL\Query;

class Where {
    public readonly string $column;
    public readonly WhereOperators $operator;
    public readonly mixed $value;

    public function __construct(string $column, WhereOperators $operator, mixed $value) {
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function toString(): string {
        return "$this->column {$this->operator->toString()} :$this->column";
    }
}