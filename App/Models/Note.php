<?php

namespace App\Models;

use Core\Model;

class Note extends Model
{
    protected static ?string $tableName = 'notes';

    public int $user_id;
    public ?int $folder_id;

    public string $title;
    public ?string $content;

    public ?bool $pinned, $completed;
    public ?string $created_at, $updated_at;

}
