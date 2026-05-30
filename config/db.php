<?php

/*
|------------------------------------------------>
| DATABASE CONFIGURATION
|------------------------------------------------>
| Render PostgreSQL (SSL REQUIRED)
|------------------------------------------------>
*/

/*
|------------------------------------------------>
| ENVIRONMENT VARIABLES (OPTIONAL)
|------------------------------------------------>
*/

$host = getenv("DB_HOST") ?: "dpg-d8aek6reo5us739k65g0-a.oregon-postgres.render.com";

$port = getenv("DB_PORT") ?: "5432";

$dbname = getenv("DB_NAME") ?: "letunite";

$user = getenv("DB_USER") ?: "auto";

$password = getenv("DB_PASSWORD") ?: "Q93h1EybyEv2m85WnWOxi6UdEgr1ifyr";

/*
|------------------------------------------------>
| PDO CONNECTION
|------------------------------------------------>
*/

try {

    $pdo = new PDO(

        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",

        $user,
        $password,

        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    die("DATABASE CONNECTION FAILED: " . $e->getMessage());
}
?>
