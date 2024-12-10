<?php

namespace App\Validators;

abstract class BaseValidator
{
    static protected array $rules = [], $errors = [], $skip = [];

    static public function validate(array $fields = []): bool
    {
        if (empty(static::$rules)) {
            return true;
        }

        foreach ($fields as $key => $value) {
            if (in_array($key, static::$skip)) {
                if (!empty(static::$errors[$key])) {
                    unset(static::$errors[$key]);
                }
                continue;
            }

            if (!empty(static::$rules[$key]) && preg_match(static::$rules[$key], $value)) {
                unset(static::$errors[$key]);
            }
        }

        return empty(static::$errors);
    }

    static public function getErrors(): array
    {
        return static::$errors;
    }

    static public function setError(string $key, string $message): void
    {
        static::$errors[$key] = $message;
    }
}
