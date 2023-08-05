<?php

namespace SimpleFW\Database\SQL\Query;

class Query {
    public readonly string $query;
    public readonly array $arguments;

    public function __construct(string $query, array $arguments) {
        $this->query = $query;
        $this->arguments = $arguments;
    }
}