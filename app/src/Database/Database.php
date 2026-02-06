<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = 'php-oop-exercice-db';
            $db = 'blog';
            $user = 'root';
            $password = 'password';

            $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

            try {
                self::$instance = new PDO($dsn, $user, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
