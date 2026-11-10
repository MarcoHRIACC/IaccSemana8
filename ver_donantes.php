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

$resultado = $conexion->query('SELECT * FROM proyecto ORDER BY id ASC');

if ($resultado === false) {
    echo '<h2>Error al consultar los proyectos</h2>';
    echo '<p>' . htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8') . '</p>';
    $conexion->close();
    exit;
}

$proyectos = $resultado->fetch_all(MYSQLI_ASSOC);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Proyectos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Lista de Proyectos</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Responsable</th>
            <th>Presupuesto</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($proyectos as $fila): ?>
        <tr>
            <td><?= (int)$fila['id'] ?></td>
            <td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['responsable'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>$<?= number_format((float)$fila['presupuesto'], 2, ',', '.') ?></td>
            <td><?= htmlspecialchars($fila['estado'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
