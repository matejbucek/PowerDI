<?php

namespace PowerDI\Database;
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Converter {
    private string $class;

    public function __construct(string $class) {
        $this->class = $class;
    }

    public function getClass(): string {
        return $this->class;
    }
}