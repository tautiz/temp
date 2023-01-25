<?php

namespace Appsas;

use Appsas\Exceptions\PageNotFoundException;
use Exception;

class App
{
    protected static Container $container;

    public static function setContainer(Container $container)
    {
        static::$container = $container;
    }

    /**
     * @throws PageNotFoundException
     */
    public static function run(): void
    {
        /** @var Request $request */
        $request = static::resolve(Request::class);
        /** @var Router $router */
        $router = static::resolve(Router::class);
        /** @var Response $response */
        $response = $router->dispatch($request);
        $response->send();
    }

    public static function resolve($class)
    {
        return static::$container->resolve($class);
    }

    /**
     * @throws Exception
     */
    public static function bind($class, $concrete): void
    {
        static::$container->bind($class, $concrete);
    }
}