<?php

/**
 * Database Configuration — MySQLi singleton
 * Provides iud() for INSERT/UPDATE/DELETE and search() for SELECT queries.
 */
class Database
{
    public static $connection;

    public static function setUpConnection()
    {
        if (!isset(self::$connection)) {
            self::$connection = new mysqli(
                env('DB_HOST', 'localhost'),
                env('DB_USER', 'root'),
                env('DB_PASS', ''),
                env('DB_NAME', 'new_path_2'),
                env('DB_PORT', '3306')
            );

            if (self::$connection->connect_error) {
                die("Database connection failed: " . self::$connection->connect_error);
            }

            self::$connection->set_charset('utf8mb4');
        }
    }

    /**
     * Execute INSERT, UPDATE, DELETE queries
     */
    public static function iud($q)
    {
        self::setUpConnection();
        self::$connection->query($q);
    }

    /**
     * Execute SELECT queries — returns mysqli_result
     */
    public static function search($q)
    {
        self::setUpConnection();
        $rs = self::$connection->query($q);
        return $rs;
    }
}
