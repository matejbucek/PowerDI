<?php

namespace PowerDI\Emails;

class Address {
    private string $address;
    private string $alias;

    /**
     * @param string $address
     * @param string $alias
     */
    public function __construct(string $address, string $alias) {
        $this->address = $address;
        $this->alias = $alias;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function getAlias(): string {
        return $this->alias;
    }
}
