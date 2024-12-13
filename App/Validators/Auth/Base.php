<?php

namespace App\Validators\Auth;

use App\Models\User;
use App\Validators\BaseValidator;

abstract class Base extends BaseValidator
{
    static protected array $rules = [
        'email' => '/^[a-zA-Z0-9.!#$%&\'*+\/\?^_`{|}~-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i',
        'password' => '/[a-zA-Z0-9.!#$%&\'*+\/\?^_`{|}~-]{8,}/',
    ];

    static public function checkEmailOnExists(string $email, string $message, bool $eqError = true): bool
    {
        $result = User::where('email', value: $email)->exists();

        if ($result === $eqError) {
            static::setError('email', $message);
        }

        return $result;
    }
}
