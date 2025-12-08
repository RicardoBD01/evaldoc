<?php
define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/evaldoc");

// EVALDOC/config.php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use PDOException;

// Cargar variables de entorno desde .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

function db(): PDO {
    $connection = $_ENV['DB_CONNECTION'] ?? 'pgsql';
    $host       = $_ENV['DB_HOST'] ?? 'localhost';
    $port       = $_ENV['DB_PORT'] ?? 5432;
    $dbname     = $_ENV['DB_DATABASE'] ?? '';
    $user       = $_ENV['DB_USERNAME'] ?? '';
    $password   = $_ENV['DB_PASSWORD'] ?? '';

    $dsn = "$connection:host=$host;port=$port;dbname=$dbname";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return $pdo;
    } catch (PDOException $e) {
        // En desarrollo puedes mostrarlo; en producción solo log
        die("Error de conexión a la base de datos: " . $e->getMessage());
    }
}
