<?php

namespace SimpleFW\Security;

class Principal {
    protected string $name;
    protected array $roles;
    protected array $data;

    public function __construct(string $name, array $roles, array $data) {
        $this->name = $name;
        $this->roles = $roles;
        $this->data = $data;
    }

    public function getName() {
        return $this->name;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getData() {
        return $this->data;
    }
}

