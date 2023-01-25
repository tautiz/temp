<?php

namespace Appsas;

use Closure;
use Exception;

class Container
{
    protected array $bindings = [];

    /**
     * @throws Exception
     */
    public function bind(string $class, $concrete): void
    {
        $this->bindings[$class] = $concrete;
    }

    public function resolve(string $class)
    {
        if (!isset($this->bindings[$class])) {
            $this->bind($class, new $class());

            return $this->resolve($class);
        }

        return $this->bindings[$class];
    }
}