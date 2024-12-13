<?php

# route
use Core\Router;

Router::post('api/register')
    ->controller(\App\Controllers\AuthController::class)
    ->action('register');

Router::post('api/auth')
    ->controller(\App\Controllers\AuthController::class)
    ->action('auth');


Router::get('api/v1/folders')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('index');
Router::get('api/v1/folders/{id:\d+}')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('show');
Router::post('api/v1/folders/store')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('store');
Router::put('api/v1/folders/{id:\d+}/update')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('update');
Router::delete('api/v1/folders/{id:\d+}/destroy')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('destroy');
