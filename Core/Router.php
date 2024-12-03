<?php

namespace Core;

use App\Enums\Http\Status;
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

    protected array $convertTypes = [
        'd' => 'int',
        '.' => 'string'
    ];

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
                /**
                 * array:3 [▼
                 * 0 => "admin/users/25"
                 * "id" => "25"
                 * 1 => "25"
                 * ]
                 */
                $this->params = $this->buildParams($regex, $matches, $params);
                /**
                 * [
                 *  'controller' => ...
                 *  'action' => ....
                 *  'id' => ...
                 * ]
                 */
                return true;
            }
        }

        throw new Exception(__CLASS__ . ": Route [$uri] not found", 404);
    }

    protected function buildParams(string $regex, array $matches, array $params): array
    {
        preg_match_all('/\(\?P<[\w]+>\\\\?([\w\.][\+]*)\)/', $regex, $types);

        if ($types) {
            $uriParams = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            $lastKey = array_key_last($types);
            $step = 0;
            $types = array_map(
                fn($value) => str_replace('+', '', $value),
                $types[$lastKey]
            );

            foreach ($uriParams as $key => $value) {
                settype($value, $this->convertTypes[$types[$step]]);
                $params[$key] = $value;
                $step++;
            }
        }

        return $params;
    }

    protected function checkHttpMethod(): void
    {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD']); // GET POST PUT ...

        if ($requestMethod !== $this->params['method']) {
            throw new Exception("Method [$requestMethod] not allowed!", Status::METHOD_NOT_ALLOWED->value);
        }

        unset($this->params['method']);
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

    static public function dispatch(string $uri): string
    {
        $router = static::getInstance();
        $uri = $router->removeQueryVariables($uri);
        $uri = trim($uri, '/');
        if ($router->match($uri)) {
            $router->checkHttpMethod();

            $controller = new $router->params['controller'];
            $action = $router->params['action'];

            unset($router->params['controller']);
            unset($router->params['action']);

            if ($controller->before($action, $router->params)) {
                $response = call_user_func_array([$controller, $action], $router->params);

                $controller->after($action, $response);

                return jsonResponse(
                    $response['status'],
                    [
                        'data' => $response['body'],
                        'errors' => $response['errors']
                    ]
                );
            }
        }

        return jsonResponse(
            Status::INTERNAL_SERVER_ERROR,
            [
                'data' => [],
                'errors' => 'Internal Server Error'
            ]
        );
    }
}
