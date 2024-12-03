<?php

namespace Core;

use App\Enums\Http\Status;

abstract class Controller
{
    public function before(string $action, array $params = []): bool
    {
        return true;
    }

    public function after(string $action, array $response = []): void
    {
        if (empty($response)) {
            throw new \Exception(__CLASS__ . "::$action - empty response");
        }
    }

    protected function response(Status $status, array $body, array $errors = []): array
    {
        return compact('status', 'body', 'errors');
    }
}
