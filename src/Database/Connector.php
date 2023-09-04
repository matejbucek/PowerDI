<?php

namespace PowerDI\Database;

interface Connector {
    public function prepare(string $stmt): \PDOStatement;
    public function getType(): RepositoryType;
}