<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexion.php";

// Solo permitir GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido"
    ]);

    exit;
}

try {

    $consulta = $conexion->prepare(
        "SELECT id, nombre
         FROM tipos_residuo
         ORDER BY id ASC"
    );

    $consulta->execute();

    $tipos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Convertir los ID a entero
    foreach ($tipos as &$tipo) {
        $tipo["id"] = (int) $tipo["id"];
    }

    echo json_encode([
        "ok" => true,
        "tipos" => $tipos
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Error al obtener los tipos de residuo"
    ]);
}