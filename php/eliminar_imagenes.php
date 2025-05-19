<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id']) || empty($_POST['imagenes_eliminar'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$imagenes_eliminar = $_POST['imagenes_eliminar'];

// Verificar que las imágenes pertenecen al usuario antes de eliminarlas
$placeholders = implode(',', array_fill(0, count($imagenes_eliminar), '?'));
$sql = "UPDATE imagenes SET estado = 'eliminado' WHERE id IN ($placeholders) AND usuario_id = ?";
$stmt = $conn->prepare($sql);

// Crear array de parámetros (IDs de imágenes + usuario_id)
$params = $imagenes_eliminar;
$params[] = $usuario_id;

// Ejecutar la consulta
$stmt->bind_param(str_repeat('i', count($params)), ...$params);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['mensaje'] = "Imágenes eliminadas correctamente";
} else {
    $_SESSION['error'] = "Error al eliminar las imágenes";
}

$stmt->close();
$conn->close();

header("Location: ../InicioSesion/usuario//miPerfil.php");
exit();
?>