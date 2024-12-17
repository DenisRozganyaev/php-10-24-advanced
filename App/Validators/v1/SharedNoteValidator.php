<?php

namespace App\Validators\v1;

use App\Models\Note;
use App\Models\SharedNote;
use App\Models\User;
use App\Validators\BaseValidator;

class SharedNoteValidator extends BaseValidator
{
    protected static array $rules = [
        'user_id' => '/\d+/i',
        'note_id' => '/\d+/i',
    ];

    protected static array $errors = [
        'user_id' => 'User ID must be an integer',
        'note_id' => 'Note ID must be an integer',
    ];

    public static function validate(array $fields = []): bool
    {
        $result =  [
            parent::validate($fields),
            # is user exists
            static::isUserExists($fields['user_id']),
            # shared user is not a note owner
            static::sharedUserIsNotOwner($fields['user_id'], $fields['note_id']),
            # is not shared with user
            static::isNotSharedWithUser($fields['user_id'], $fields['note_id']),
        ];

        return !in_array(false, $result);
    }

    static protected function isNotSharedWithUser(int $userId, int $noteId): bool
    {
        $exists = SharedNote::where('user_id', value: $userId)
            ->and('note_id', value: $noteId)
            ->exists();

        if ($exists) {
            static::setError('message', 'Note already shared with this user.');
        }

        return !$exists;
    }

    static protected function sharedUserIsNotOwner(int $userId, int $noteId): bool
    {
        $note = Note::find($noteId);

        if (!$note) {
            static::setError('note_id', 'Note ID is invalid');
            return false;
        }

        $result = $note->user_id !== $userId;

        if (!$result) {
            static::setError('user_id', 'You could not share a note to yourself');
        }

        return $result;
    }

    static protected function isUserExists(int $userId): bool
    {
        $exists = User::where('id', value: $userId)->exists();

        if (!$exists) {
            static::setError('user_id', 'User ID is invalid');
        }

        return $exists;
    }
}
