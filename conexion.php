<?php

header("Content-Type: application/json; charset=UTF-8");

$host = getenv("DB_HOST") ?: "localhost";
$port = getenv("DB_PORT") ?: "3306";
$baseDatos = getenv("DB_NAME") ?: "ecolim";
$usuario = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASSWORD") ?: "";

$certificado = file_exists("/etc/secrets/ca.pem")
    ? "/etc/secrets/ca.pem"
    : __DIR__ . "/certificados/ca.pem";

try {

    $dsn = "mysql:"
        . "host=$host;"
        . "port=$port;"
        . "dbname=$baseDatos;"
        . "charset=utf8mb4;"
        . "sslmode=verify-ca;"
        . "sslrootcert=" . $certificado;

    $conexion = new PDO(
        $dsn,
        $usuario,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Error de conexión a la base de datos"
    ]);

    exit;
}