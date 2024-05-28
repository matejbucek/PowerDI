<?php

namespace PowerDI\Templates\Forms\Validators;

use PowerDI\Templates\Forms\Control;
use PowerDI\Templates\Forms\Validator;

class NotEmpty implements Validator {
    private function __construct() {}
    private static $instance = null;
    public static function instance(): NotEmpty {
        if(self::$instance === null) {
            self::$instance = new NotEmpty();
        }
        return self::$instance;
    }
    public function validate(Control &$control): bool {
        return $control->getValue() !== null;
    }

    public function asJson(): array {
        return [];
    }
}