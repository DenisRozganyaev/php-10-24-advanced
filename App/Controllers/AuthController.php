<?php

namespace App\Controllers;

use Core\Controller;

class AuthController extends Controller
{
    public function register()
    {
        $fields = requestBody();
        dd($fields);
    }

    public function auth()
    {
    }
}
