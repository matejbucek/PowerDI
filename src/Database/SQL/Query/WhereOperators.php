<?php

namespace SimpleFW\Database\SQL\Query;

enum WhereOperators {
    case Equal;
    case GreaterThan;
    case LessThan;
    case GreaterOrEqual;
    case LessOrEqual;
    case NotEqual;

    public function toString(): string {
        return match($this) {
            WhereOperators::Equal => "=",
            WhereOperators::GreaterThan => ">",
            WhereOperators::LessThan => "<",
            WhereOperators::GreaterOrEqual => ">=",
            WhereOperators::LessOrEqual => "<=",
            WhereOperators::NotEqual => "<>",
        };
    }
}