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

$sql = 'SELECT D.id, P.nombre AS proyecto, N.nombre AS donante, D.monto, D.fecha
        FROM donacion D
        JOIN proyecto P ON D.id_proyecto = P.id
        JOIN donante N ON D.id_donante = N.id
        ORDER BY D.id ASC';

$resultado = $conexion->query($sql);

if ($resultado === false) {
    echo '<h2>Error al consultar las donaciones</h2>';
    echo '<p>' . htmlspecialchars($conexion->error, ENT_QUOTES, 'UTF-8') . '</p>';
    $conexion->close();
    exit;
}

$donaciones = $resultado->fetch_all(MYSQLI_ASSOC);
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Donaciones</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Listado de Donaciones</h2>
    <table>
        <tr>
            <th>#</th>
            <th>Proyecto</th>
            <th>Donante</th>
            <th>Monto</th>
            <th>Fecha</th>
        </tr>
        <?php foreach ($donaciones as $index => $fila): ?>
        <tr>
            <td><?= (int)($index + 1) ?></td>
            <td><?= htmlspecialchars($fila['proyecto'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($fila['donante'], ENT_QUOTES, 'UTF-8') ?></td>
            <td>$<?= number_format((float)$fila['monto'], 2, ',', '.') ?></td>
            <td><?= htmlspecialchars($fila['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
