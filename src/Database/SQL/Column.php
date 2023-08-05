<?php

namespace SimpleFW\Database\SQL;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column {

    private string $name;
    private string $type;

    public function __construct(string $name, string $type) {
        $this->name = $name;
        $this->type = $type;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
        return $this->type;
    }
}