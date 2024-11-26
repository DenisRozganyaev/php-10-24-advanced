<?php

namespace Core;

use Core\Traits\RouteHttpMethods;
use Exception;

class Router
{
    use RouteHttpMethods;

    static protected ?Router $instance = null;

    /**
     * @var array $routes - contain routes with controllers, actions etc...
     * @var array $params - contain request params
     */
    protected array $routes = [], $params = [];
    protected string $currentRoute;

    static public function getInstance(): static
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @throws \Exception
     */
    public function controller(string $controller): static
    {
        if (!class_exists($controller)) {
            throw new Exception("Controller {$controller} not found!");
        }

        if (!in_array(get_parent_class($controller), [Controller::class])) {
            throw new Exception("Controller {$controller} does not extend " . Controller::class);
        }

        $this->routes[$this->currentRoute]['controller'] = $controller;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function action(string $action): void
    {
        if (empty($this->routes[$this->currentRoute]['controller'])) {
            throw new Exception("Controller not found inside the route!");
        }

        $controller = $this->routes[$this->currentRoute]['controller'];

        // App/Controllers/AuthController::class
        if (!method_exists($controller, $action)) {
            throw new Exception("Controller $controller does not contain [$action] action");
        }

        $this->routes[$this->currentRoute]['action'] = $action;
    }

    /**
     * @param string $uri - users/45/edit?test=true&admin=1.... => $_GET
     * @return string
     */
    protected function removeQueryVariables(string $uri): string
    {
        return preg_replace('/([\w\/\d]+)(\?[\w=\d\&\%\[\]\-\_\:\+\"\"\'\']+)/i', '$1', $uri);
    }

    protected function match(string $uri): bool
    {
        foreach($this->routes as $regex => $params) {
            if (preg_match($regex, $uri, $matches)) {
                return true;
            }
        }

        throw new Exception(__CLASS__ . ": Route [$uri] not found", 404);
    }

    /**
     * $uri = admin/notes
     * $routes = [
     *      'admin/notes' => []
     * ]
     * @param string $uri
     * @return static
     */
    static protected function setUri(string $uri): static
    {
        // $uri -> users/{id:\d+}/edit
        // 'users\\/{id:\d+}\\/edit'
        $uri = preg_replace('/\//', '\\/', $uri);
        // users\\/(?P<id>\d+)\\/edit
        $uri = preg_replace('/\{([a-zA-Z_-]+):([^}]+)}/', '(?P<$1>$2)', $uri);
        // ['id' => 4]
        $uri = "/^$uri$/i";

        $router = static::getInstance();
        $router->routes[$uri] = [];
        $router->currentRoute = $uri;

        return $router;
    }

    static public function dispatch(string $uri)
    {
        $router = static::getInstance();
        $uri = $router->removeQueryVariables($uri);
        $uri = trim($uri, '/');
        if ($router->match($uri)) {
            dd($uri);
        }
    }
}
