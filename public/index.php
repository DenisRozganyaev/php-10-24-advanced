<?php

use App\Enums\Http\Status;
use App\Enums\SQL;
use App\Models\User;
use Core\Router;
use Dotenv\Dotenv;

define('BASE_DIR', dirname(__DIR__)); // /var/www/html

require_once BASE_DIR . '/vendor/autoload.php';

try {
    Dotenv::createUnsafeImmutable(BASE_DIR)->load();

    require_once BASE_DIR . '/routes/api.php';

    die(Router::dispatch($_SERVER['REQUEST_URI']));
} catch (Throwable $exception) {
    die(
        jsonResponse(
            $exception->getCode() === 0 ? Status::UNPROCESSABLE_ENTITY : Status::from($exception->getCode()),
            [
                'errors' => [
                    'message' => $exception->getMessage(),
                    'trace' => $exception->getTrace(),
                ]
            ]
        )
    );
}
