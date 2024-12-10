<?php

# route
use Core\Router;

Router::post('api/register')
    ->controller(\App\Controllers\AuthController::class)
    ->action('register');

Router::post('api/auth')
    ->controller(\App\Controllers\AuthController::class)
    ->action('auth');
