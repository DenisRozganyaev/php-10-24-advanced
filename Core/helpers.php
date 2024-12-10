<?php

use App\Enums\Http\Status;
use Core\DB;

function requestBody(): array
{
    $requestBody = file_get_contents('php://input');
    $body = json_decode($requestBody, true);

    $fields = !json_last_error() ? $body : [];

    return array_map(fn ($value) => is_bool($value) ? (int) $value : $value, $fields);
}

function jsonResponse(Status $status, array $data = []): string
{
    header_remove();
    http_response_code($status->value);
    header("Content-Type: application/json");
    header("Status: $status->value");

    return json_encode([
        ...$status->withDescription(),
        'data' => $data
    ]);
}

function db(): PDO
{
    return DB::connect();
}
