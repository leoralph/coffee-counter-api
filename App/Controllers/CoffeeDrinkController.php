<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserCoffeeDrink;
use App\Request;
use DateTime;

class CoffeeDrinkController
{
    public function drink(Request $request)
    {
        if (!$request->amount) return ["status_code" => 406, "errors" => ["Amount field is required."]];
        if (!is_int($request->amount)) return ["status_code" => 406, "errors" => ["Amount field must be an integer."]];

        UserCoffeeDrink::create([
            'user_id' => $request->param('iduser'),
            'amount' => $request->amount,
            'dranked_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function userHistory(Request $request)
    {
        $user = User::findByColumn('id', $request->param('iduser'));
        if (!$user) return ['status_code' => 404, 'errors' => ['User not found.']];
        
        $history = UserCoffeeDrink::findHistoryByUser($request->param('iduser'));
        if (!$user) return ['status_code' => 404, 'errors' => ['No records found for specified user.']];


        return [
            'user' => [
                'iduser' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'history' => array_map(function ($record) {
                return ['date' => $record->dranked_at, 'amount' => $record->amount];
            }, $history)
        ];

    }

    public function userRanking(Request $request)
    {
        if (!$request->date && !$request->last_days) return ['status_code' => 406, 'errors' => ['You must send the filter type (last_days or date).']];
        if ($request->date && $request->last_days) return ['status_code' => 406, 'errors' => ['You must send only one filter type.']];


        if ($request->date) {
            $date = DateTime::createFromFormat('Y-m-d', $request->date);

            if (!$date) return ['status_code' => 406, 'errors' => ['You must send a valid date using the Y-m-d format.']];

            $condition = "DATE(`dranked_at`) = ?";
            $parameters = [$request->date];
        } else {

            if (!is_numeric($request->last_days)) return ['status_code' => 406, 'errors' => ['Field last_days must be an integer.']];

            $date = (new DateTime())->modify("-$request->last_days DAYS");

            $condition = "DATE(`dranked_at`) >= ?";
            $parameters = [$date->format('Y-m-d')];
        }

        $ranking = UserCoffeeDrink::findHigherDrinkingUserHistory($condition, $parameters);

        if (!$ranking) return ['status_code' => 404, 'errors' => ['No records found for the specified filter.']];

        $user = User::findByColumn('id', $ranking->user_id);

        return [
            'higher_drinking_user' => [
                'iduser' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'times' => $ranking->times
        ];
    }
}
