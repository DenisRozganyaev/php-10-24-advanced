<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected static ?string $tableName = 'users';

    public string $email, $password, $created_at;
    public ?string $token, $token_expired_at;

    public function getAllInfo(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
