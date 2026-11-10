<?php
/**
 * Archivo de configuración para conexión a MySQL.
 * Puedes definir estas variables en tu entorno del servidor:
 * DB_HOST, DB_NAME, DB_USER, DB_PASS
 */

function obtenerConexion(): mysqli
{
    $host = getenv('DB_HOST') ?: 'localhost';
    $db   = getenv('DB_NAME') ?: 'organizacion_app';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    $conexion = new mysqli($host, $user, $pass, $db);

    if ($conexion->connect_error) {
        throw new RuntimeException('Error de conexión a la base de datos: ' . $conexion->connect_error);
    }

    $conexion->set_charset('utf8mb4');

    return $conexion;
}
