<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;

class AuthController extends Controller
{
    public function register()
    {
        $users = User::select()->get();
        foreach ($users as $user) {
            d($user->getAllInfo());
        }
    }
}
