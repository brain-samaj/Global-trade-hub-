<?php

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

/*
|--------------------------------------------------------------------------
| LOAD ENV FILE
|--------------------------------------------------------------------------
*/

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

/*
|--------------------------------------------------------------------------
| GET ENV VARIABLES (SAFE)
|--------------------------------------------------------------------------
*/

$host = $_ENV["DB_HOST"] ?? null;
$port = $_ENV["DB_PORT"] ?? "5432";
$dbname = $_ENV["DB_NAME"] ?? null;
$user = $_ENV["DB_USER"] ?? null;
$password = $_ENV["DB_PASSWORD"] ?? null;

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (!$host || !$dbname || !$user || !$password) {
    die("
❌ ENV ERROR: Missing database credentials<br>
HOST: $host<br>
DB: $dbname<br>
USER: $user<br>
PASSWORD: " . ($password ? "SET" : "MISSING")
    );
}

/*
|--------------------------------------------------------------------------
| POSTGRESQL CONNECTION (RENDER SAFE + SSL REQUIRED)
|--------------------------------------------------------------------------
*/

try {

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

} catch (PDOException $e) {
    die("❌ DATABASE CONNECTION FAILED: " . $e->getMessage());
}

?>	
