<?php

namespace PowerDI\Database;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Repository {
    protected string $class;
    protected RepositoryType $type;

    public function __construct(string $class, RepositoryType $type = RepositoryType::MySQL) {
        $this->class = $class;
        $this->type = $type;
    }

    public function getClass(): string {
        return $this->class;
    }

    public function getType(): RepositoryType {
        return $this->type;
    }
}