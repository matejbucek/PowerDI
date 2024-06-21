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
        $this->isValid = true;
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
        foreach ($this->controls as $name => &$control) {
            if ($control->getType() == ControlType::File) {
                $control->setValue($request->getFile($name));
            } else if ($control->getType() == ControlType::Date) {
                $control->setValue($control->getConverter() ? $control->getConverter()->dbToObject($request->getParam($name)) : null);
            } else {
                $control->setValue(htmlspecialchars($request->getParam($name)));
            }
            $this->isValid &= $control->validate();
        }
    }

    public function fill(array $filling): void {
        foreach ($this->controls as $name => &$control) {
            if (!array_key_exists($name, $filling)) {
                $control->setValue(null);
            } else if ($control->getType() == ControlType::File) {
                $control->setValue($filling[$name]);
            } else if ($control->getType() == ControlType::Date) {
                if ($filling[$name] instanceof \DateTime) {
                    $control->setValue($filling[$name]);
                } else {
                    $control->setValue($control->getConverter() ? $control->getConverter()->dbToObject($filling[$name]) : null);
                }
            } else if($control->getConverter() != null) {
                $control->setValue($control->getConverter()->dbToObject($filling[$name]));
            } else {
                $control->setValue(htmlspecialchars($filling[$name]));
            }
            $this->isValid &= $control->validate();
        }
    }

    public function isValid(): bool {
        return $this->isValid;
    }

    public function asJson(): string {
        $json = [];

        foreach ($this->controls as $name => $control) {
            $json["controls"][$name] = $control->asJson();
        }
        return json_encode($json);
    }
}