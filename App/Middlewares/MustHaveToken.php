<?php

namespace App\Middlewares;

use App\Models\User;
use App\Request;

class MustHaveToken
{
    public string $message = 'Request must have a valid Bearer Token';
    public int $code = 403;

    public function __invoke(Request $request)
    {
        if (!$request->getBearerToken()) return false;

        $user = User::findByColumn('api_token', $request->getBearerToken());

        if (empty($user)) return false;

        $request->setUser($user);
    }
}
