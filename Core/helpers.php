<?php

use App\Enums\Http\Status;
use Core\DB;

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
