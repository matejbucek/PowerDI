<?php

namespace SimpleFW\Database;

use SimpleFW\Database\SQL\Column;
use SimpleFW\Loaders\ComponentLoader;

class Helpers {

    public static function columnNameOr(\ReflectionProperty $property, string $or): string {
        if(ComponentLoader::hasAttribute($property, Column::class)) {
            return ComponentLoader::instantiateAttribute($property, Column::class)->getName();
        }
        return $or;
    }
}