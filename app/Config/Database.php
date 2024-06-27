<?php

namespace Nurdin\Djawara\Config;

use Dotenv\Dotenv;
use PDO;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnect(): PDO
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->load();
        if (self::$connection == null) {
            $host = "localhost";
            $port = 3306;
            $dbName = "djawara_barbershop";
            $username = "root";
            $password = $_ENV["DB_PASS"];

            self::$connection = new PDO("mysql:host=$host:$port;dbname=$dbName", $username, $password);
        }
        return self::$connection;
    }

    public static function beginTransaction()
    {
        self::$connection->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$connection->commit();
    }

    public static function rollbackTransaction()
    {
        self::$connection->rollBack();
    }
}
