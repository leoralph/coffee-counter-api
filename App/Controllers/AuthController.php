<?php

namespace App\Controllers;

use App\Models\User;
use App\Request;

class AuthController
{
    public function login(Request $request)
    {
        $errors = [];

        if (!$request->email) $errors[] = "Email field is required";
        if (!$request->password) $errors[] = "Password field is required";

        $user = User::findByEmailAndPassword($request->email, md5($request->password));

        if (!$user) return ['status_code' => 401, 'errors' => ['User does not exist or password is invalid']];

        return [
            'api_token' => $user->api_token
        ];
    }
}
