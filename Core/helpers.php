<?php

use App\Enums\Http\Status;
use App\Models\Folder;
use Core\DB;
use ReallySimpleJWT\Token;

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

function getAuthToken(): string
{
    $headers = apache_request_headers();

    if (empty($headers['Authorization'])) {
        throw new Exception('Authorization header not set', 401);
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);

    if (!Token::validateExpiration($token)) {
        throw new Exception('Authorization token expired', 401);
    }

    return $token;
}

function authId(): int
{
    $token = Token::getPayload(getAuthToken());

    if (empty($token['user_id'])) {
        throw new Exception('Token structure is invalid', 422);
    }

    return (int) $token['user_id'];
}

function commonFolders(): array
{
    $defaults = array_values(Folder::DEFAULTS);
    $folders = [];

    foreach ($defaults as $folder) {
        $folders[] = strtoupper($folder);
        $folders[] = ucfirst($folder);
        $folders[] = strtolower($folder);
    }

    return $folders;
}
