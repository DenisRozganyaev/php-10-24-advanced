<?php

namespace Core;

abstract class Controller
{
    public function before(string $action, array $params = []): bool
    {
        return true;
    }

    public function after(string $action, array $response): void
    {

    }
}
