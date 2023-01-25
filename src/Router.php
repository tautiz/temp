<?php

namespace Appsas;

use Appsas\Exceptions\PageNotFoundException;
use Appsas\Request;
use Exception;

class Router
{
    /**
     * @param Output $output
     * @param array $routes
     */
    public function __construct(protected Output $output, private array $routes = [])
    {
    }

    /**
     * Prideda Routus į $this->routes masyvą
     *
     * @param string $method
     * @param string $url
     * @param array $controllerData
     */
    public function addRoute(string $method, string $url, array $controllerData): void
    {
        $this->routes[$method][$url] = $controllerData;
    }

    public function get(string $url, array $controllerData): void
    {
        $this->addRoute('GET', $url, $controllerData);
    }

    public function post(string $url, array $controllerData): void
    {
        $this->addRoute('POST', $url, $controllerData);
    }

    /**
     * @throws PageNotFoundException
     * @throws Exception
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $url = $request->getUrl();
        $url = explode('?', $url)[0];
        $url = rtrim($url, '/');
        $url = ltrim($url, '/');

        if (isset($this->routes[$method][$url])) {
            $controllerData = $this->routes[$method][$url];
            $controller = $controllerData[0];
            $action = $controllerData[1];

            $response = $controller->$action($request);

            if($response instanceof Response && $response->redirect) {
                header('location: ' . $response->redirectUrl);
                $response->redirect = false;
                exit;
            }

            if (!$response instanceof Response) {
                throw new Exception("Controllerio $controller metodas '$action' turi grąžinti Response objektą");
            }

            return $response;
        } else {
            throw new PageNotFoundException("Adresas: [$method] /$url nerastas");
        }
    }
}