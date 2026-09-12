<?php

header("Content-Type: application/json; charset=UTF-8");

$host = getenv("DB_HOST");
$port = getenv("DB_PORT");
$baseDatos = getenv("DB_NAME");
$usuario = getenv("DB_USER");
$password = getenv("DB_PASSWORD");

$certificado = "/etc/secrets/ca.pem";

try {

    $dsn = "mysql:host=$host;port=$port;dbname=$baseDatos;charset=utf8mb4";

    $conexion = new PDO(
        $dsn,
        $usuario,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            PDO::MYSQL_ATTR_SSL_CA => $certificado,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ]
    );

    $resultado = $conexion->query("SELECT VERSION()")->fetchColumn();

    echo json_encode([
        "ok" => true,
        "mensaje" => "Conexión PDO con Aiven exitosa",
        "version_mysql" => $resultado
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Error de conexión",
        "error" => $e->getMessage()
    ]);
}