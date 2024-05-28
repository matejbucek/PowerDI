<?php

namespace PowerDI\Cache;

class MemcachedService implements Cache {
    private \Memcached $memcached;

    public function __construct(array $config) {
        $this->memcached = new \Memcached();
        if (isset($config['servers'])) {
            foreach ($config['servers'] as $server) {
                if (!isset($server['host']) || !isset($server['port'])) {
                    throw new \Exception('Memcached server configuration is invalid');
                }
                $this->memcached->addServer($server['host'], $server['port']);
            }
        }

        if (isset($config['sasl'])) {
            if (isset($config['sasl']['username']) && isset($config['sasl']['password'])) {
                $this->memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
                $this->memcached->setSaslAuthData($config['sasl']['username'], $config['sasl']['password']);
            }
        }
    }

    public function get(string $key): mixed {
        return $this->memcached->get($key);
    }

    public function set(string $key, mixed $value, int $ttl): void {
        $this->memcached->set($key, $value, $ttl);
    }
}