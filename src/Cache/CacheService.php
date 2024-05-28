<?php

namespace PowerDI\Cache;

interface CacheService {
    public function get(string $key): mixed;
    public function set(string $key, mixed $value, int $ttl = 0): void;
    public function delete(string|array $keys): void;
    public function clear(): void;
}