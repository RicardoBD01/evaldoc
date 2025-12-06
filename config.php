<?php
define("BASE_PATH", $_SERVER['DOCUMENT_ROOT'] . "/evaldoc");

// =========================================
// Cargar Composer (phpdotenv)
// =========================================
require __DIR__ . '/vendor/autoload.php';

// =========================================
// Cargar archivo .env
// =========================================
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();  // safeLoad evita error si el archivo no existe

// =========================================
// Configuración global
// =========================================
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');

// =========================================
// Función para manejar errores
// =========================================
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// =========================================
// Conexión PDO a base de datos PostgreSQL
// =========================================
function db() {
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

        if (APP_DEBUG) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        } else {
            error_log("DB ERROR: " . $e->getMessage());
            die("Error en el servidor. Intente más tarde.");
        }
    }
}