<?php

namespace App\Enums\Http;

enum Status: int
{
    case OK = 200;
    case CREATED = 201;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case UNPROCESSABLE_ENTITY = 422;
    case INTERNAL_SERVER_ERROR = 500;

    public function withDescription(): array
    {
        $description = match($this->value) {
            200 => 'Ok',
            201 => 'Created',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            405 => 'Method not allowed',
            422 => 'Unprocessable entity',
            500 => 'Internal Server error',
        };

        return [
            'code' => $this->value,
            'status' => $this->value . ' ' . $description
        ];
    }
}
