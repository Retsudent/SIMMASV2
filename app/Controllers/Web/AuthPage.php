<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class AuthPage extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function logout()
    {
        return view('auth/logout');
    }
}


