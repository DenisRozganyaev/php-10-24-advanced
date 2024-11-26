<?php

use Core\Router;
use Dotenv\Dotenv;

define('BASE_DIR', dirname(__DIR__)); // /var/www/html

require_once BASE_DIR . '/vendor/autoload.php';

try {
    Dotenv::createUnsafeImmutable(BASE_DIR)->load();

    require_once BASE_DIR . '/routes/api.php';

    Router::dispatch($_SERVER['REQUEST_URI']);
} catch (Throwable $exception) {
    dd($exception);
}
