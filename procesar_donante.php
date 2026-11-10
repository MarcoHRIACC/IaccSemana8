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

$nombre = trim($_POST['nombre_donante'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$monto = filter_input(INPUT_POST, 'monto_donado', FILTER_VALIDATE_FLOAT);

$errores = [];

if ($nombre === '' || strlen($nombre) < 3) {
    $errores[] = 'El nombre del donante debe tener al menos 3 caracteres.';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo electrónico no es válido.';
}

if ($monto === false || $monto <= 0) {
    $errores[] = 'El monto donado debe ser mayor que cero.';
}

if (!empty($errores)) {
    echo '<h2>Se encontraron errores:</h2>';
    echo '<ul>';
    foreach ($errores as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="formulario_donante.html">Volver al formulario</a></p>';
    $conexion->close();
    exit;
}

$stmt = $conexion->prepare('INSERT INTO donante (nombre, correo, telefono, monto_donado) VALUES (?, ?, ?, ?)');
$stmt->bind_param('sssd', $nombre, $correo, $telefono, $monto);

if ($stmt->execute()) {
    echo 'Donante registrado correctamente.<br><a href="ver_donantes.php">Ver donantes</a>';
} else {
    http_response_code(500);
    echo 'Error al registrar el donante: ' . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
}

$stmt->close();
$conexion->close();
