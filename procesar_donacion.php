<?php
$conexion = new mysqli("localhost", "root", "", "organizacion_app");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre= $_POST ['nombre'];
$correo= $_POST ['correo'];
$monto= $_POST ['monto'];


$stmt = $conexion->prepare("INSERT INTO proyecto (nombre, correo,monto) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $nombre,$correo, $monto);

if ($stmt->execute()) {
    echo "Gracias por tu donación de " . $monto . " a " . $nombre ;
} else {
    echo "Error al procesar donacion, verifique los datos" . $stmt->error;
}

$stmt->close();
$conexion->close();
?>