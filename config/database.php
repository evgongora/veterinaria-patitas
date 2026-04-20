<?php

declare(strict_types=1);

/**
 * Conexión PDO para la 
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $host = getenv('DB_HOST');
        if ($host === false || $host === '') {
            $host = 'db';
        }

        $port = getenv('DB_PORT');
        if ($port === false || $port === '') {
            $port = '3306';
        }

        $name = getenv('DB_NAME');
        if ($name === false || $name === '') {
            $name = 'appdb';
        }

        $user = getenv('DB_USER');
        if ($user === false || $user === '') {
            $user = 'appuser';
        }

        $pass = getenv('DB_PASS');
        if ($pass === false) {
            $pass = 'apppass';
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $name
        );

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        return self::$pdo;
    }

    /**
     * @deprecated Preferir Database::getConnection() (PDO). Se mantiene por compatibilidad.
     */
    public function connect()
    {
        $host = getenv('DB_HOST');
        if ($host === false || $host === '') {
            $host = 'db';
        }

        $conn = new mysqli(
            $host,
            getenv('DB_USER') ?: 'appuser',
            getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'apppass',
            getenv('DB_NAME') ?: 'appdb'
        );

        if ($conn->connect_error) {
            die('Error conexión: ' . $conn->connect_error);
        }

        return $conn;
    }
}
