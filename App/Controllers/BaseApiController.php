<?php

namespace App\Controllers;

use App\Enums\Http\Status;
use App\Models\User;
use Core\Controller;
use Core\Model;
use Exception;
use ReallySimpleJWT\Token;

abstract class BaseApiController extends Controller
{
    protected ?Model $model = null;

    abstract protected function getModel(): string;

    public function before(string $action, array $params = []): bool
    {
        $token = getAuthToken();
        $user = User::findBy('token', $token);

        if (!$user || !Token::validate($token, $user->password)) {
            throw new Exception('Token Invalid', 422);
        }

        $payload = Token::getPayload($token);

        if (!empty($payload['exp']) && $payload['exp'] < $user->token_expired_at) {
            throw new Exception('Token Expired', 422);
        }

        $this->checkResourceOwner($action, $params);

        return true;
    }

    protected function checkResourceOwner(string $action, array $params): void
    {
        if (in_array($action, ['show', 'update', 'destroy'])) {
            $obj = call_user_func_array([$this->getModel(), 'find'], $params);

            if (!$obj) {
                throw new Exception('Resource not found', Status::NOT_FOUND->value);
            }

            if (!is_null($obj?->user_id) && $obj?->user_id !== authId()) {
                throw new Exception('This resource is forbidden', Status::FORBIDDEN->value);
            }

            $this->model = $obj;
        }
    }
}
