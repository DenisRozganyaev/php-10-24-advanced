<?php

namespace App\Validators\Auth;

class RegisterValidator extends Base
{
    protected static array $errors = [
        'email' => 'Email is incorrect',
        'password' => 'Password is incorrect. Minimum length should be 8 or more'
    ];

    public static function validate(array $fields = []): bool
    {
        $result =  [
            parent::validate($fields),
            !static::checkEmailOnExists($fields['email'], 'Email already exists!')
        ];

        return !in_array(false, $result);
    }
}
