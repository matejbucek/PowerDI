<?php

namespace PowerDI\Cache;

interface Cache {
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl): void;
}