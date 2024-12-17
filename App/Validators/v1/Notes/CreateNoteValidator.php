<?php

namespace App\Validators\v1\Notes;

class CreateNoteValidator extends Base
{
    public static function validate(array $fields = []): bool
    {
        $result =  [
            parent::validate($fields),
            static::validateFolderId($fields['folder_id']),
            !static::checkTitleOnDuplicate($fields['title'], $fields['folder_id']),
            static::isBoolean($fields, 'pinned'),
            static::isBoolean($fields, 'completed'),
        ];

        return !in_array(false, $result);
    }
}
