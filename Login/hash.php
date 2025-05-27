<?php
include '../Conexion.php'; // Ajusta si está en otro path

$correo = 'admin@admin.com';
$clave_plana = '123';
$clave_encriptada = password_hash($clave_plana, PASSWORD_DEFAULT);
$rol = 'admin';

$sql = "INSERT INTO usuarios (correo, clave, rol, numero_documento)
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $correo, $clave_encriptada, $rol, $documento);

if ($stmt->execute()) {
    echo "✅ Usuario admin creado correctamente.";
} else {
    echo "❌ Error al crear el usuario: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
