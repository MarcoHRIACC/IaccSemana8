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

$resultado = $conexion->query('SELECT * FROM donante ORDER BY id ASC');

if ($resultado === false) {
    echo '<h2>Error al consultar los donantes</h2>';
    echo '<p>' . htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8') . '</p>';
    $conexion->close();
    exit;
}

$donantes = $resultado->fetch_all(MYSQLI_ASSOC);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Donantes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Lista de Donantes</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Monto Donado</th>
        </tr>
        <?php foreach ($donantes as $fila): ?>
        <tr>
            <td><?= (int)$fila['id'] ?></td>
            <td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['correo'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['telefono'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>$<?= number_format((float)$fila['monto_donado'], 2, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
