<?php

namespace PowerDI\Templates\Forms;

use PowerDI\Database\Convertable;

class Control implements \Stringable {
    private ControlType $type;
    private array $validators;
    private mixed $value;
    private ?Convertable $converter;

    /**
     * @param ControlType $type
     * @param array $validators
     * @param mixed $value
     */
    public function __construct(ControlType $type, array $validators, mixed $value = null, ?Convertable $converter = null) {
        $this->type = $type;
        $this->validators = $validators;
        if (!$value) {
            $this->value = $this->type == ControlType::Text ? "" : null;
        } else {
            $this->value = $value;
        }
        $this->converter = $converter;
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

    public function setValue(mixed $value) {
        $this->value = $value;
    }

    public function getConverter(): ?Convertable {
        return $this->converter;
    }

    public function validate(): bool {
        $isValid = true;
        foreach ($this->validators as &$validator) {
            $isValid &= $validator->validate($this);
        }
        return $isValid;
    }

    public function __toString(): string {
        if ($this->value == null) {
            return "";
        }

        if ($this->type == ControlType::Text) {
            return $this->value;
        } else if ($this->type == ControlType::Date) {
            return $this->converter ? $this->converter->objectToDB($this->value) : "";
        } else {
            return "";
        }
    }
}