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

$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$responsable = trim($_POST['responsable'] ?? '');
$presupuesto = filter_input(INPUT_POST, 'presupuesto', FILTER_VALIDATE_FLOAT);
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;
$estado = trim($_POST['estado'] ?? '');

$errores = [];

if ($nombre === '' || strlen($nombre) < 3) {
    $errores[] = 'El nombre del proyecto debe tener al menos 3 caracteres.';
}

if ($descripcion === '') {
    $errores[] = 'La descripción es obligatoria.';
}

if ($responsable === '') {
    $errores[] = 'El responsable es obligatorio.';
}

if ($presupuesto === false || $presupuesto <= 0) {
    $errores[] = 'El presupuesto debe ser un número mayor que cero.';
}

if ($fecha_inicio === null || $fecha_inicio === '') {
    $errores[] = 'La fecha de inicio es obligatoria.';
}

if ($estado === '') {
    $errores[] = 'Debe seleccionar un estado.';
}

if ($fecha_fin !== null && $fecha_fin !== '' && $fecha_inicio !== null && $fecha_inicio !== '' && strtotime($fecha_fin) < strtotime($fecha_inicio)) {
    $errores[] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
}

if (!empty($errores)) {
    echo '<h2>Se encontraron errores:</h2>';
    echo '<ul>';
    foreach ($errores as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="formulario_proyecto.html">Volver al formulario</a></p>';
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare('INSERT INTO proyecto (nombre, descripcion, responsable, presupuesto, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->bind_param('sssdsss', $nombre, $descripcion, $responsable, $presupuesto, $fecha_inicio, $fecha_fin, $estado);

if ($stmt->execute()) {
    echo 'Proyecto registrado correctamente.<br><a href="ver_proyectos.php">Ver proyectos</a>';
} else {
    http_response_code(500);
    echo 'Error al registrar el proyecto: ' . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
}

$stmt->close();
$conexion->close();
