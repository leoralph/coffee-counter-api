<?php 

namespace App\Models;

use App\Application;
use PDO;

class UserCoffeeDrink
{
    public static function create(array $attributes)
    {
        $stmt = Application::pdo()->prepare("INSERT INTO `user_coffee_drinks` (`user_id`, `amount`, `dranked_at`) VALUES (?, ?, ?)");

        $stmt->execute([
            $attributes['user_id'],
            $attributes['amount'],
            $attributes['dranked_at']
        ]);
    }

    public static function findHistoryByUser(int $userId)
    {
        $stmt = Application::pdo()->prepare("
            SELECT `user_id`, SUM(`amount`) AS amount, DATE(`dranked_at`) AS dranked_at
            FROM `user_coffee_drinks`
            WHERE `user_id` = ?
            GROUP BY DATE(`dranked_at`)
            ORDER BY `dranked_at` DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function findHigherDrinkingUserHistory(string $condition, array $parameters)
    {
        $stmt = Application::pdo()->prepare("
            SELECT `user_id`, sum(`amount`) AS `times`
            FROM `user_coffee_drinks` 
            WHERE $condition
            GROUP BY `user_id` 
            ORDER BY `times` DESC
            LIMIT 1
        ");

        $stmt->execute($parameters);

        $userAndTimes = $stmt->fetchObject();

        return $userAndTimes;
    }
}