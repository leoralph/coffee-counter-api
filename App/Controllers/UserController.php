<?php

namespace App\Controllers;

use App\Models\User;
use App\Request;

class UserController
{

    public function all(Request $request)
    {
        $options = [];

        if ($request->page) {
            $options['limit'] = ($request->page - 1) * 10 . ",10";
        }

        $users = User::findAll($options);

        if (empty($users)) {
            return [
                'status_code' => 404,
                'message' => 'No users found.'
            ];
        }

        return array_map(function ($user) {
            return [
                'iduser' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'drinkCounter' => intval($user->drink_counter)
            ];
        }, $users);
    }

    public function find(Request $request)
    {
        $user = User::findByColumn('id', $request->param('iduser'));

        if (empty($user)) {
            return [
                'status_code' => 404,
                'message' => 'User not found.'
            ];
        }

        return [
            'iduser' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'drinkCounter' => intval($user->drink_counter)
        ];
    }

    public function delete(Request $request)
    {
        if ($request->getUser()->id != $request->param('iduser')) {
            return [
                'status_code' => 403,
                'errors' => [
                    'You can only delete your own user.'
                ]
            ];
        }

        User::deleteById($request->param('iduser'));

        return ['status_code' => 204];
    }

    public function update(Request $request)
    {
        if ($request->getUser()->id != $request->param('iduser')) {
            return ['status_code' => 403, 'errors' => ['You can only update your own user.']];
        }

        if (!$request->email && !$request->name && !$request->password)
            return ['status_code' => 406, 'errors' => ['You must send at least one updatable field.']];

        $update = [];

        if ($request->email) $update['email'] = $request->email;
        if ($request->name) $update['name'] = $request->name;

        if ($request->password) {
            $update['password'] = md5($request->password);
            $update['api_token'] = md5(time());
        }

        User::update($update, $request->param('iduser'));

        return ['status_code' => 204];
    }

    public function create(Request $request)
    {
        $errors = [];

        if (!$request->name) $errors[] = "Name field is required.";
        if (!$request->email) $errors[] = "Email field is required.";
        if (!$request->password) $errors[] = "Password field is required.";

        if (!empty($errors)) return ['status_code' => 406, "errors" => $errors];

        $userWithSameEmail = User::findByColumn('email', $request->email);

        if ($userWithSameEmail) return ['status_code' => 406, "errors" => ["User already exists."]];

        $apiToken = md5(time());
        $password = md5($request->password);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password,
            'api_token' => $apiToken
        ]);

        return ['status_code' => 201];
    }
}
