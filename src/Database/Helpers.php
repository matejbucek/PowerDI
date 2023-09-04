<?php

namespace PowerDI\Database;

use PowerDI\Database\SQL\Column;
use PowerDI\Loaders\ComponentLoader;

class Helpers {

    public static function columnNameOr(\ReflectionProperty $property, string $or): string {
        if(ComponentLoader::hasAttribute($property, Column::class)) {
            return ComponentLoader::instantiateAttribute($property, Column::class)->getName();
        }
        return $or;
    }
}