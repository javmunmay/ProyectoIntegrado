<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=no_autenticado");
    exit();
}

// Verificar método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../contacto.php?error=metodo_no_valido");
    exit();
}

// Validar campos obligatorios
if (empty($_POST['titulo']) || empty($_POST['descripcion'])) {
    header("Location: ../contacto.php?error=campos_obligatorios");
    exit();
}

// Obtener datos del formulario
$titulo = $conn->real_escape_string($_POST['titulo']);
$descripcion = $conn->real_escape_string($_POST['descripcion']);
$prioridad = isset($_POST['prioridad']) ? $conn->real_escape_string($_POST['prioridad']) : 'media';
$usuario_id = $_SESSION['user_id'];

// Insertar incidencia en la base de datos
$stmt = $conn->prepare("INSERT INTO incidencias 
                        (usuario_id, titulo, descripcion, prioridad) 
                        VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $usuario_id, $titulo, $descripcion, $prioridad);

if ($stmt->execute()) {
    // Obtener datos del usuario para notificación
    $sql_usuario = "SELECT nombre, correo FROM Usuarios WHERE id = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $usuario_id);
    $stmt_usuario->execute();
    $result_usuario = $stmt_usuario->get_result();
    $usuario = $result_usuario->fetch_assoc();
    $stmt_usuario->close();
    
    
    header("Location: ../contacto.php?success=incidencia_registrada");
    exit();
} else {
    header("Location: ../contacto.php?error=error_bd");
    exit();
}

?>