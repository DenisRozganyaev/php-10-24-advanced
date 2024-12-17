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
Router::get('api/v1/folders/{id:\d+}/notes')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('notes');
Router::post('api/v1/folders/store')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('store');
Router::put('api/v1/folders/{id:\d+}/update')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('update');
Router::delete('api/v1/folders/{id:\d+}/destroy')
    ->controller(\App\Controllers\v1\FoldersController::class)
    ->action('destroy');


Router::get('api/v1/notes')
    ->controller(\App\Controllers\v1\NotesController::class)
    ->action('index');
Router::get('api/v1/notes/{id:\d+}')
    ->controller(\App\Controllers\v1\NotesController::class)
    ->action('show');
Router::post('api/v1/notes/store')
    ->controller(\App\Controllers\v1\NotesController::class)
    ->action('store');
Router::put('api/v1/notes/{id:\d+}/update')
    ->controller(\App\Controllers\v1\NotesController::class)
    ->action('update');
Router::delete('api/v1/notes/{id:\d+}/destroy')
    ->controller(\App\Controllers\v1\NotesController::class)
    ->action('destroy');

Router::post('api/v1/notes/{note_id:\d+}/share/add')
    ->controller(\App\Controllers\v1\SharedNoteController::class)
    ->action('add');

Router::delete('api/v1/notes/{note_id:\d+}/share/remove')
    ->controller(\App\Controllers\v1\SharedNoteController::class)
    ->action('remove');
