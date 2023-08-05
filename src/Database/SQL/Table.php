<?php

namespace SimpleFW\Database\SQL;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Table {

    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
    public function getName(): string {
        return $this->name;
    }
}