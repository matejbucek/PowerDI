<?php

namespace PowerDI\Templates\Forms;

interface Validator {
    public function validate(Control &$control): bool;
    public function asJson(): array;
}