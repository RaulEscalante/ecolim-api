<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "conexion.php";

try {

    $metodo = $_SERVER["REQUEST_METHOD"];

    // =========================================================
    // GET - OBTENER RESIDUOS
    // =========================================================

    if ($metodo === "GET") {

        $usuarioId = $_GET["usuario_id"] ?? null;
        if (!$usuarioId) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "El usuario_id es obligatorio"
            ]);
            exit;
        }

        $consulta = $conexion->prepare(
            "SELECT
                id,
                usuario_id,
                tipo_residuo_id,
                cantidad,
                unidad,
                DATE_FORMAT(fecha, '%d/%m/%Y') AS fecha,
                ubicacion,
                observaciones,
                sincronizado
             FROM residuos
             WHERE usuario_id = ?
             ORDER BY id DESC"
        );

        $consulta->execute([
            $usuarioId
        ]);

        $residuos = $consulta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($residuos as &$residuo) {
            $residuo["id"] = (int) $residuo["id"];
            $residuo["usuario_id"] = (int) $residuo["usuario_id"];
            $residuo["tipo_residuo_id"] = (int) $residuo["tipo_residuo_id"];
            $residuo["cantidad"] = (float) $residuo["cantidad"];
            $residuo["sincronizado"] = (int) $residuo["sincronizado"];
        }

        echo json_encode([
            "ok" => true,
            "residuos" => $residuos
        ]);

        exit;
    }


    // =========================================================
    // POST - INSERTAR RESIDUO
    // =========================================================
    if ($metodo === "POST") {
        $datos = json_decode(file_get_contents("php://input"), true);

        if ($datos === null) {
            http_response_code(400);
            echo json_encode(["ok" => false, "mensaje" => "Datos JSON inválidos"]);
            exit;
        }

        $usuarioId = $datos["usuario_id"] ?? null;
        $tipoResiduoId = $datos["tipo_residuo_id"] ?? null;
        $cantidad = $datos["cantidad"] ?? null;
        $unidad = trim($datos["unidad"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $ubicacion = trim($datos["ubicacion"] ?? "");
        $observaciones = trim($datos["observaciones"] ?? "");

        if (!$usuarioId || !$tipoResiduoId || $cantidad === null || $unidad === "" || $fecha === "") {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "Faltan datos obligatorios"
            ]);

            exit;
        }


        // Convertir dd/MM/yyyy → yyyy-MM-dd
        $fechaConvertida = DateTime::createFromFormat( "d/m/Y", $fecha);

        if (!$fechaConvertida) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "Formato de fecha inválido"
            ]);
            exit;
        }

        $fechaMysql = $fechaConvertida->format("Y-m-d");

        $consulta = $conexion->prepare(
            "INSERT INTO residuos (
                usuario_id,
                tipo_residuo_id,
                cantidad,
                unidad,
                fecha,
                ubicacion,
                observaciones,
                sincronizado
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)"
        );

        $consulta->execute([
            $usuarioId, $tipoResiduoId, $cantidad, $unidad,
            $fechaMysql, $ubicacion, $observaciones
        ]);

        $id = $conexion->lastInsertId();
        echo json_encode([
            "ok" => true,
            "mensaje" => "Residuo registrado correctamente",
            "id" => (int) $id
        ]);
        exit;
    }


    // =========================================================
    // PUT - ACTUALIZAR RESIDUO
    // =========================================================
    if ($metodo === "PUT") {
        $id = $_GET["id"] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "El id del residuo es obligatorio"
            ]);
            exit;
        }

        $datos = json_decode( file_get_contents("php://input"), true);
        if ($datos === null) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "Datos JSON inválidos"
            ]);
            exit;
        }

        $usuarioId = $datos["usuario_id"] ?? null;
        $tipoResiduoId = $datos["tipo_residuo_id"] ?? null;
        $cantidad = $datos["cantidad"] ?? null;
        $unidad = trim($datos["unidad"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $ubicacion = trim($datos["ubicacion"] ?? "");
        $observaciones = trim($datos["observaciones"] ?? "");

        if (!$usuarioId || !$tipoResiduoId || $cantidad === null || $unidad === "" || $fecha === "") {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "Faltan datos obligatorios"
            ]);
            exit;
        }

        // Convertir dd/MM/yyyy → yyyy-MM-dd
        $fechaConvertida = DateTime::createFromFormat( "d/m/Y", $fecha);
        if (!$fechaConvertida) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "Formato de fecha inválido"
            ]);
            exit;
        }

        $fechaMysql = $fechaConvertida->format("Y-m-d");
        $consulta = $conexion->prepare(
            "UPDATE residuos
             SET
                tipo_residuo_id = ?,
                cantidad = ?,
                unidad = ?,
                fecha = ?,
                ubicacion = ?,
                observaciones = ?
             WHERE id = ?
             AND usuario_id = ?"
        );

        $consulta->execute([
            $tipoResiduoId,
            $cantidad,
            $unidad,
            $fechaMysql,
            $ubicacion,
            $observaciones,
            $id,
            $usuarioId
        ]);

        if ($consulta->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se encontró el residuo"
            ]);
            exit;
        }

        echo json_encode([
            "ok" => true,
            "mensaje" => "Residuo actualizado correctamente"
        ]);
        exit;
    }

    // =========================================================
    // DELETE - ELIMINAR RESIDUO
    // =========================================================
    if ($metodo === "DELETE") {
        $id = $_GET["id"] ?? null;
        $usuarioId = $_GET["usuario_id"] ?? null;
        if (!$id || !$usuarioId) {
            http_response_code(400);
            echo json_encode([
                "ok" => false,
                "mensaje" => "id y usuario_id son obligatorios"
            ]);
            exit;
        }

        $consulta = $conexion->prepare(
            "DELETE FROM residuos
             WHERE id = ?
             AND usuario_id = ?"
        );

        $consulta->execute([
            $id,
            $usuarioId
        ]);

        if ($consulta->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se encontró el residuo"
            ]);
            exit;
        }

        echo json_encode([
            "ok" => true,
            "mensaje" => "Residuo eliminado correctamente"
        ]);
        exit;
    }


    // =========================================================
    // MÉTODO NO PERMITIDO
    // =========================================================
    http_response_code(405);
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "ok" => false,
        "mensaje" => "Error al procesar la operación"
    ]);
}