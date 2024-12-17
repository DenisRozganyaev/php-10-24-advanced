<?php

namespace App\Validators\v1\Notes;

use App\Enums\SQL;
use App\Models\Folder;
use App\Models\Note;

class UpdatedNoteValidator extends Base
{
    static protected ?int $id;

    public static function validate(array $fields = []): bool
    {
        static::$id = $fields['id'];

        $result =  [
            parent::validate($fields),
            static::validateFolderId($fields['folder_id']),
            !static::checkTitleOnDuplicate($fields['title'], $fields['folder_id']),
            static::isBoolean($fields, 'pinned'),
            static::isBoolean($fields, 'completed'),
        ];

        return !in_array(false, $result);
    }

    protected static function checkTitleOnDuplicate(string $title, int $folderId): bool
    {
        $isExists = Note::where('title', value: $title)
            ->and('user_id', value: authId())
            ->and('folder_id', value: $folderId)
            ->and('id', SQL::NOT_EQUAL, static::$id)
            ->exists();

        if ($isExists) {
            static::setError('title', "Note with title '$title' already exists");
        }

        return $isExists;
    }
}
