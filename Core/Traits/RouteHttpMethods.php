<?php

namespace Core\Traits;

use App\Enums\Http\Methods;

trait RouteHttpMethods
{
    static public function get(string $uri): static
    {
        return static::setUri($uri)->setHttpMethod(Methods::GET);
    }

    static public function post(string $uri): static
    {
        return static::setUri($uri)->setHttpMethod(Methods::POST);
    }

    static public function put(string $uri): static
    {
        return static::setUri($uri)->setHttpMethod(Methods::PUT);
    }

    static public function delete(string $uri): static
    {
        return static::setUri($uri)->setHttpMethod(Methods::DELETE);
    }

    protected function setHttpMethod(Methods $method): static
    {
        // $method->name, $method->value
        $this->routes[$this->currentRoute]['method'] = $method->value;
        return $this;
    }
}
