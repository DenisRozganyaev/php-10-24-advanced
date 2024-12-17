<?php

namespace App\Models;

use Core\Model;

class Folder extends Model
{
    const array DEFAULTS = [
        'general' => 'General',
        'shared' => 'Shared',
    ];

    protected static ?string $tableName = 'folders';

    public ?int $user_id;

    public string $title;
    public ?string $created_at, $updated_at;
}