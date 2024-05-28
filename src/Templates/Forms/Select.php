<?php

namespace PowerDI\Templates\Forms;

class Select extends Control {
    private array $options;

    public function __construct(array $options, array $validators, mixed $value = null, ?Convertable $converter = null) {
        parent::__construct(ControlType::Select, $validators, $value, $converter);
        $this->options = $options;
    }

    public function getOptions(): array {
        return $this->options;
    }
}