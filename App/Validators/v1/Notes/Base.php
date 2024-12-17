<?php

namespace App\Validators\v1\Notes;

use App\Models\Folder;
use App\Models\Note;
use App\Validators\BaseValidator;

class Base extends BaseValidator
{
    protected static array $skip = [
        'user_id',
        'pinned',
        'completed',
        'created_at',
        'updated_at',
    ];

    protected static array $rules = [
        'title' => '/[\w\s\(\)\-]{3,}/i',
        'folder_id' => '/\d+/i'
    ];

    protected static array $errors = [
        'title' => 'Title should contain only characters, numbers and _-() and has length more than 2 symbols',
        'folder_id' => '[folder_id] should be exists and has type integer'
    ];

    static protected function isBoolean(array $fields, string $key): bool
    {
        if (empty($fields[$key])) {
            return true;
        }

        $result = is_bool($fields[$key]) || $fields[$key] === 1;

        if (!$result) {
            static::setError($key, "[$key] should be boolean");
        }

        return $result;
    }

    static protected function validateFolderId(int $folderId): bool
    {
        $folder = Folder::find($folderId);

        if ($folder) {
            return is_null($folder->user_id) || $folder->user_id === authId();
        }

        return false;
    }

    static protected function checkTitleOnDuplicate(string $title, int $folderId): bool
    {
        $isExists = Note::where('title', value: $title)
            ->and('user_id', value: authId())
            ->and('folder_id', value: $folderId)
            ->exists();

        if ($isExists) {
            static::setError('title', "Note with title '$title' already exists");
        }

        return $isExists;
    }
}
