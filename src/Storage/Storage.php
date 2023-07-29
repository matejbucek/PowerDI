<?php

namespace SimpleFW\Storage;

interface Storage {
    public function has(string $property): bool;
    public function get(string $property);
    public function set(string $property, $value): void;
    public function delete(string $property): void;
}