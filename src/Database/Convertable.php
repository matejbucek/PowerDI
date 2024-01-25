<?php

namespace PowerDI\Database;

interface Convertable {
    public function objectToDB(mixed $object): mixed;
    public function dbToObject(mixed $db): mixed;
}