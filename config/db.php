<?php

/*
|--------------------------------------------------------------------------
| RENDER POSTGRESQL CONNECTION (PRODUCTION SAFE)
|--------------------------------------------------------------------------
| Uses Render Environment Variables ONLY
| No .env file needed
| Works on live deployment
*/

$host = getenv("DB_HOST");
$port = getenv("DB_PORT") ?: "5432";
$dbname = getenv("DB_NAME");
$user = getenv("DB_USER");
$password = getenv("DB_PASSWORD");

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if (!$host || !$dbname || !$user || !$password) {
    die("
❌ ENV ERROR: Missing database credentials<br>
HOST: " . ($host ?? 'MISSING') . "<br>
DB: " . ($dbname ?? 'MISSING') . "<br>
USER: " . ($user ?? 'MISSING') . "<br>
PASSWORD: " . ($password ? 'SET' : 'MISSING') . "
    ");
}

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTION
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
