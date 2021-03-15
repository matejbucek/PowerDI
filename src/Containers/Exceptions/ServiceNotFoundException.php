<?php
namespace SimpleFW\Containers\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends \Exception implements NotFoundExceptionInterface
{
}

