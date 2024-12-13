<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\User;
use App\Validators\Auth\AuthValidator;
use App\Validators\Auth\RegisterValidator;
use Core\Controller;
use ReallySimpleJWT\Token;

class AuthController extends Controller
{
    public function register()
    {
        $fields = requestBody();

        if (RegisterValidator::validate($fields)) {
            $user = User::createAndReturn([
                ...$fields,
                'password' => password_hash($fields['password'], PASSWORD_ARGON2ID)
            ]);

            return $this->response(Status::OK, $user->toArray());
        }

        return $this->response(
            Status::UNPROCESSABLE_ENTITY,
            $fields,
            RegisterValidator::getErrors()
        );
    }

    public function auth()
    {
        $fields = requestBody();

        if (AuthValidator::validate($fields)) {
            $user = User::findBy('email', $fields['email']);

            if (password_verify($fields['password'], $user->password)) {
                $expired_at = time() + 3600;
                $token = Token::create($user->id, $user->password, $expired_at, 'localhost');

                $user->update([
                    'token' => $token,
                    'token_expired_at' => $expired_at
                ]);

                return $this->response(Status::OK, compact('token'));
            }
        }

        return $this->response(
            Status::UNPROCESSABLE_ENTITY,
            $fields,
            AuthValidator::getErrors()
        );
    }
}
