<?php

namespace App;

use App\Exceptions\MiddlewareException;
use App\Exceptions\NotFoundException;

class Router
{
    private array $routes = [
        'get' => [],
        'post' => [],
        'put' => [],
        'delete' => [],
    ];

    private function add(string $method, string $route, callable|array $callback, array $middlewares = [])
    {
        $this->routes[$method][$route] = [...$callback, $middlewares];
    }

    public function run()
    {
        $request = new Request;

        try {
            $callback = $this->tryFindCallback($request);

            list($class, $method, $middlewareArray) = $callback;

            $this->tryRunMiddlewares($middlewareArray, $request);

            $controller = new $class;

            $response = call_user_func_array([$controller, $method], [$request]);
        } catch (MiddlewareException $e) {
            $response = ['status_code' => $e->getCode(), 'errors' => [$e->getMessage()]];
        } catch (NotFoundException $e) {
            $response = ['status_code' => $e->getCode(), 'errors' => ['Page not found']];
        }

        if (isset($response['status_code'])) {
            http_response_code($response['status_code']);
            unset($response['status_code']);
        }

        header('Content-Type: application/json');

        echo json_encode($response);
    }

    private function tryFindCallback(Request $request)
    {
        $uri = $request->uri;
        $method = $request->method;

        $callback = $this->routes[$method][$uri] ?? null;

        if (!empty($callback)) return $callback;

        $explodedUri = array_values(array_filter(explode('/', $uri)));

        foreach ($this->routes[$method] as $route => $callback) {
            $parameters = [];

            $explodedRoute = array_values(array_filter(explode('/', $route)));

            if (count($explodedRoute) != count($explodedUri)) continue;

            foreach (array_diff($explodedRoute, $explodedUri) as $key => $paramName) {
                if (!str_starts_with($paramName, ':')) continue 2;

                $parameters[ltrim($paramName, ':')] = $explodedUri[$key];
            }

            $request->setParameters($parameters);

            return $callback;
        }

        throw new NotFoundException("Request route not found", 404);
    }

    private function tryRunMiddlewares(array $middlewares, Request $request)
    {
        foreach ($middlewares as $className) {
            $middleware = new $className;

            $middlewareCheck = $middleware($request);

            if ($middlewareCheck === false) {
                throw new MiddlewareException($middleware->message, $middleware->code);
            }
        }
    }

    public function get(string $route, callable|array $callback, array $middlewares = [])
    {
        $this->add('get', $route, $callback, $middlewares);
    }

    public function post(string $route, callable|array $callback, array $middlewares = [])
    {
        $this->add('post', $route, $callback, $middlewares);
    }

    public function put(string $route, callable|array $callback, array $middlewares = [])
    {
        $this->add('put', $route, $callback, $middlewares);
    }

    public function delete(string $route, callable|array $callback, array $middlewares = [])
    {
        $this->add('delete', $route, $callback, $middlewares);
    }
}
