<?php
namespace PowerDI\Core;

interface Context
{
    public function put($key, $value);
    public function get($key);
}

