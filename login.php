<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexion.php";

// Solo permitir POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido"
    ]);

    exit;
}

// Leer JSON recibido
$datos = json_decode(
    file_get_contents("php://input"),
    true
);

// Validar que exista el JSON
if ($datos === null) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Datos JSON inválidos"
    ]);

    exit;
}

// Obtener datos
$usuario = trim($datos["usuario"] ?? "");
$password = trim($datos["password"] ?? "");

// Validar campos
if ($usuario === "" || $password === "") {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Usuario y contraseña son obligatorios"
    ]);

    exit;
}

try {

    $consulta = $conexion->prepare(
        "SELECT id, usuario, password
         FROM usuarios
         WHERE usuario = ?
         LIMIT 1"
    );

    $consulta->execute([
        $usuario
    ]);

    $usuarioEncontrado =
        $consulta->fetch(PDO::FETCH_ASSOC);

    // Usuario no encontrado
    if (!$usuarioEncontrado) {

        http_response_code(401);

        echo json_encode([
            "ok" => false,
            "mensaje" => "Usuario o contraseña incorrectos"
        ]);

        exit;
    }

    // Comparar contraseña
    if ($password !== $usuarioEncontrado["password"]) {

        http_response_code(401);

        echo json_encode([
            "ok" => false,
            "mensaje" => "Usuario o contraseña incorrectos"
        ]);

        exit;
    }

    // Login correcto
    echo json_encode([
        "ok" => true,
        "mensaje" => "Inicio de sesión correcto",
        "usuario" => [
            "id" => (int) $usuarioEncontrado["id"],
            "usuario" => $usuarioEncontrado["usuario"]
        ]
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "mensaje" => "Error al realizar el inicio de sesión"
    ]);
}