<?php

namespace App;

use PDO;

class Application
{
    private static self $instance;
    private static bool $booted = false;

    private array $config = [];
    private PDO $pdo;
    private string $rootPath;

    public static function boot(string $rootPath, bool $databaseSetup = false)
    {
        if (self::$booted) return;

        self::$instance = new self($rootPath);
        self::$booted = true;

        if ($databaseSetup) self::$instance->runDatabaseSchemaSetup();
    }

    private function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->config = require "$this->rootPath/config.php";
        $this->bootPDO();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function bootPDO()
    {
        $dbConfig = $this->config['database'];

        $dsn = "mysql:dbname=$dbConfig[db_name];host=$dbConfig[host]";

        $this->pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
    }

    private function runDatabaseSchemaSetup()
    {
        $setupScript = file_get_contents("$this->rootPath/database-setup.sql");

        $this->pdo->exec($setupScript);
    }

    public static function pdo()
    {
        return self::$instance->pdo;
    }
}
