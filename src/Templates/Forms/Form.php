<?php

namespace PowerDI\Templates\Forms;

use PowerDI\Database\Convertable;
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

    public function fillFromRequest(HttpRequest $request): void {
        foreach($this->controls as $name => &$control) {
            if($control->getType() == ControlType::File) {
                $control->setValue($request->getFile($name));
            } else if($control->getType() == ControlType::Date) {
                $control->setValue($control->getConverter()? $control->getConverter()->dbToObject($request->getParam($name)) : null);
            } else {
                $control->setValue(htmlspecialchars($request->getParam($name)));
            }
            $control->validate();
        }
    }

    public function fill(array $filling): void {
        foreach($this->controls as $name => &$control) {
            if($control->getType() == ControlType::File) {
                $control->setValue($filling[$name]);
            } else if($control->getType() == ControlType::Date) {
                $control->setValue($control->getConverter()? $control->getConverter()->dbToObject($filling[$name]) : null);
            } else {
                $control->setValue(htmlspecialchars($filling[$name]));
            }
            $control->validate();
        }
    }

    public function isValid(): bool {
        return $this->isValid;
    }
}