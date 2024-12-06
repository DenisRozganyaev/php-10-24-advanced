<?php

# route
use Core\Router;

// folders/1 | folders/135
Router::get('users')
    ->controller(\App\Controllers\AuthController::class)
    ->action('register');
