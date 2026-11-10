<?php
require_once __DIR__ . '/db.php';

try {
    $conexion = obtenerConexion();
} catch (RuntimeException $e) {
    http_response_code(500);
    echo '<h2>Error de conexión</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método no permitido.';
    exit;
}

$idProyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT);
$idDonante = filter_input(INPUT_POST, 'id_donante', FILTER_VALIDATE_INT);
$monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
$fecha = $_POST['fecha'] ?? date('Y-m-d');

$errores = [];

if ($idProyecto === false || $idProyecto <= 0) {
    $errores[] = 'Debe seleccionar un proyecto válido.';
}

if ($idDonante === false || $idDonante <= 0) {
    $errores[] = 'Debe seleccionar un donante válido.';
}

if ($monto === false || $monto <= 0) {
    $errores[] = 'El monto de la donación debe ser mayor que cero.';
}

if (!empty($errores)) {
    echo '<h2>Se encontraron errores:</h2>';
    echo '<ul>';
    foreach ($errores as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="formulario_donacion.html">Volver al formulario</a></p>';
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare('INSERT INTO donacion (id_proyecto, id_donante, monto, fecha) VALUES (?, ?, ?, ?)');
$stmt->bind_param('iids', $idProyecto, $idDonante, $monto, $fecha);

if ($stmt->execute()) {
    echo 'Donación registrada correctamente.<br><a href="ver_donaciones.php">Ver donaciones</a>';
} else {
    http_response_code(500);
    echo 'Error al registrar la donación: ' . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
}

$stmt->close();
$conexion->close();
