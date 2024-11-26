<?php

# route
use Core\Router;

// folders/1 | folders/135
Router::get('admin/users/{id:\d+}')
    ->controller(\App\Controllers\AuthController::class)
    ->action('register');
