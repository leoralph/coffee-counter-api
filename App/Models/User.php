<?php

namespace App\Models;

use App\Application;
use PDO;

class User
{
    public static function findByColumn($column, $value)
    {
        $stmt = Application::pdo()->prepare("
            SELECT `u`.`id`, `u`.`name`, `u`.`email`, SUM(`ucd`.`amount`) as `drink_counter`
            FROM `users` `u` 
            LEFT JOIN `user_coffee_drinks` `ucd` ON `ucd`.`user_id` = `u`.`id` 
            WHERE `u`.`$column` = ?
            GROUP BY `u`.`id`
        ");

        $stmt->execute([$value]);

        return $stmt->fetchObject();
    }

    public static function findAll(array $options = [])
    {
        $query = "SELECT `u`.`id`, `u`.`name`, `u`.`email`, SUM(`ucd`.`amount`) as `drink_counter`
            FROM `users` `u` 
            LEFT JOIN `user_coffee_drinks` `ucd` ON `ucd`.`user_id` = `u`.`id`
            GROUP BY `u`.`id`
        ";

        if (isset($options['limit'])) {
            $query .= " LIMIT $options[limit]";
        }

        return Application::pdo()->query($query)->fetchAll(PDO::FETCH_OBJ);
    }

    public static function findByEmailAndPassword(string $email, string $password)
    {
        $stmt = Application::pdo()->prepare("SELECT `id`, `api_token` FROM `users` WHERE `email` = ? AND `password` = ?");

        $stmt->execute([$email, $password]);

        return $stmt->fetchObject();
    }

    public static function create(array $attributes)
    {
        $stmt = Application::pdo()->prepare("INSERT INTO `users` (`name`, `email`, `password`, `api_token`) VALUES (?, ?, ?, ?)");

        $stmt->execute([
            $attributes['name'],
            $attributes['email'],
            $attributes['password'],
            $attributes['api_token'],
        ]);
    }

    public static function deleteById($id)
    {
        $stmt = Application::pdo()->prepare("DELETE FROM `users` WHERE id = ?");

        $stmt->execute([$id]);
    }

    public static function update(array $attributes, int $id)
    {
        $query = "UPDATE `users` SET";

        foreach ($attributes as $columnName => $value) {
            $query .= " `$columnName` = ?";
        }

        $query .= " WHERE `id` = ?";

        $stmt = Application::pdo()->prepare($query);

        $values = array_values($attributes);

        $stmt->execute([...$values, $id]);
    }
}
