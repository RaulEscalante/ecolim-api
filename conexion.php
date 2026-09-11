<?php

header("Content-Type: application/json; charset=UTF-8");

// Configuración de la base de datos
$host = getenv("MYSQLHOST") ?: "localhost";
$puerto = getenv("MYSQLPORT") ?: "3306";
$usuario = getenv("MYSQLUSER") ?: "root";
$password = getenv("MYSQLPASSWORD") ?: "";
$baseDatos = getenv("MYSQLDATABASE") ?: "ecolim";

try {

    $conexion = new PDO(
        "mysql:host=$host;port=$puerto;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $password
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Error de conexión a la base de datos"
    ]);

    exit;
}