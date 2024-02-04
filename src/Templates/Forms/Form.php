<?php

namespace PowerDI\Templates\Forms;

use PowerDI\HttpBasics\HttpRequest;

class Form implements \ArrayAccess {
    private array $controls;
    private bool $isValid;

    /**
     * @param array $controls
     */
    public function __construct(array $controls) {
        $this->controls = $controls;
        $this->isValid = false;
    }

    public function getControls(): array {
        return $this->controls;
    }

    public function offsetExists(mixed $offset): bool {
        return array_key_exists($offset, $this->controls);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->controls[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        $this->controls[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->controls[$offset]);
    }

    public function fillFromRequest(HttpRequest $request) {
        foreach($this->controls as $name => $control) {

        }
    }
}