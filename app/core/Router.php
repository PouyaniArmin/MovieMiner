<?php

namespace App\Core;

use App\Interfaces\RouterInterface;
use App\Utility\ErrorHandler;
use Error;
use Reflection;
use ReflectionMethod;
use ReflectionNamedType;

use function PHPUnit\Framework\matches;

class Router implements RouterInterface
{
    public array $routers = [];
    public Request $request;
    public Response $response;
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
    public function get(string $path, $callback): void
    {
        $this->routers['get'][$path] = $callback;
    }
    public function post(string $path, $callback): void
    {
        $this->routers['post'][$path] = $callback;
    }
    public function put(string $path, $callback): void
    {
        $this->routers['put'][$path] = $callback;
    }
    public function patch(string $path, $callback): void
    {
        $this->routers['patch'][$path] = $callback;
    }
    public function delete(string $path, $callback): void
    {
        $this->routers['delete'][$path] = $callback;
    }


    public function resolve()
    {
        $path = $this->request->path();
        $method = $this->request->httpMethod();

        foreach ($this->routers[$method] as $route => $callback) {

            if ($this->matchRoute($route, $path, $matches)) {
                return $this->handleCallback($callback, $matches);
            }
        }
        $this->response->notFoundResponse();
        return ErrorHandler::errorMessage('Not Found Url', 1);
    }

    private function matchRoute($route, $path, &$matches)
    {

        $pattern = $this->generatePregPattern($route);
        return preg_match('#^' . $pattern . '$#', $path, $matches);
    }

    private function handleCallback($callback, $matches)
    {
        if (is_array($callback)) {
            return $this->handleControllerCallback($callback, $matches);
        }
        if (is_callable($callback)) {
            return call_user_func($callback);
        }
    }

    private function generatePregPattern($route)
    {
        return preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
    }

    private function handleControllerCallback($callback, $matches)
    {

        $controller = new $callback[0];
        $callback[0] = $controller;
        $refelction = new ReflectionMethod($controller, $callback[1]);
        $parametrs = $refelction->getParameters();
        if (!empty($parametrs) && $parametrs[0]->getType() instanceof ReflectionNamedType && $parametrs[0]->getType()->getName() === Request::class) {
            return call_user_func($callback, $this->request);
        }
        array_shift($matches);
        return call_user_func_array($callback, $matches);
    }
}
