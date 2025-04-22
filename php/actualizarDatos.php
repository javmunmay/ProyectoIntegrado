<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_admin']) || $_SESSION['user_admin'] != 1) {
    header('HTTP/1.1 403 Forbidden');
    exit(json_encode(['error' => 'Acceso no autorizado']));
}

header('Content-Type: application/json');

// Obtener estadísticas actualizadas
$stats = [
    'usuarios' => 0,
    'imagenes' => 0,
    'incidencias' => 0,
    'reportes' => 0
];

// Consulta para contar usuarios
$query = "SELECT COUNT(*) as total FROM Usuarios";
$result = $conn->query($query);
if ($result) {
    $stats['usuarios'] = $result->fetch_assoc()['total'];
}

// Consulta para contar imágenes activas
$query = "SELECT COUNT(*) as total FROM imagenes WHERE estado = 'activo'";
$result = $conn->query($query);
if ($result) {
    $stats['imagenes'] = $result->fetch_assoc()['total'];
}

// Consulta para contar incidencias pendientes
$query = "SELECT COUNT(*) as total FROM incidencias WHERE estado = 'pendiente'";
$result = $conn->query($query);
if ($result) {
    $stats['incidencias'] = $result->fetch_assoc()['total'];
}

// Consulta para contar reportes no resueltos
$query = "SELECT COUNT(*) as total FROM incidencias WHERE estado != 'resuelta'";
$result = $conn->query($query);
if ($result) {
    $stats['reportes'] = $result->fetch_assoc()['total'];
}

$conn->close();
echo json_encode($stats);
?>