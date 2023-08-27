<?php

namespace SimpleFW\Database\DTO;

#[\Attribute(\Attribute::TARGET_CLASS)]
class DTO {
    private string $class;

    public function __construct(string $class) {
        $this->class = $class;
    }

    public function getClass(): string {
        return $this->class;
    }
}