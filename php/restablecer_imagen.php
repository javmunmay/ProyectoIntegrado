<?php
session_start();
require_once 'conexion.php'; // Incluye el archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['user_id']; // Asume que tienes el ID del usuario en la sesión

    // Restablecer la imagen predeterminada
    $sql = "UPDATE usuarios SET foto_perfil = '../Usuario.jpg' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);

    if ($stmt->execute()) {
        header("Location: ../InicioSesion/InformacionPersonal.php"); // Redirigir de vuelta al perfil
        exit();
    } else {
        echo "Error al restablecer la imagen: " . $stmt->error;
    }
    $stmt->close();
}
?>