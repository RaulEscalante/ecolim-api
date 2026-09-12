<?php

header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "DB_HOST" => getenv("DB_HOST"),
    "DB_PORT" => getenv("DB_PORT"),
    "DB_NAME" => getenv("DB_NAME"),
    "DB_USER" => getenv("DB_USER"),
    "DB_PASSWORD_configurada" => getenv("DB_PASSWORD") !== false && getenv("DB_PASSWORD") !== "",
    "certificado_render" => file_exists("/etc/secrets/ca.pem"),
    "certificado_local" => file_exists(__DIR__ . "/certificados/ca.pem")
]);