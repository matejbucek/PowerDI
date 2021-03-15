<?php
namespace SimpleFW\Containers;

interface Context
{
    public function put($key, $value);
    public function get($key);
}

