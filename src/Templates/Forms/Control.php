<?php

namespace PowerDI\Templates\Forms;

class Control {
    private ControlType $type;
    private array $validators;
    private mixed $value;

    /**
     * @param ControlType $type
     * @param array $validators
     * @param mixed $value
     */
    public function __construct(ControlType $type, array $validators, mixed $value = null) {
        $this->type = $type;
        $this->validators = $validators;
        $this->value = $value;
    }

    public function getType(): ControlType {
        return $this->type;
    }

    public function getValidators(): array {
        return $this->validators;
    }

    public function getValue(): mixed {
        return $this->value;
    }
}