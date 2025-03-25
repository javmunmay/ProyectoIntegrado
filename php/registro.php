<?php
// Habilitar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectarse a la base de datos
require_once 'conexion.php';

// Incluir PHPMailer
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar si los datos requeridos están en POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre'], $_POST['correo'], $_POST['contrasena'], $_POST['confirmar_contrasena'], $_POST['politica'])) {

    // Verificar que las contraseñas coincidan
    if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
        header("Location: ../registro.php?error=Las contraseñas no coinciden");
        exit();
    }

    // Verificar si el correo ya existe
    $correo = $conn->real_escape_string($_POST['correo']);
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        header("Location: ../registro.php?error=El correo ya está registrado");
        exit();
    }

    // Recibir y sanitizar los datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $politica = isset($_POST['politica']) ? 1 : 0;

    // Insertar en la base de datos
    $sql = "INSERT INTO Usuarios (
        nombre, 
        correo, 
        contrasena, 
        usuario, 
        admin, 
        politica_privacidad
    ) VALUES (?, ?, ?, 1, 0, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $correo, $contrasena, $politica);

    if ($stmt->execute()) {
        header("Location: ../inicioSesion.php?success=Registro exitoso. Por favor inicia sesión");
        exit();
    } else {
        header("Location: ../registro.php?error=Error en el registro: " . urlencode($stmt->error));
        exit();
    }

    $stmt->close();
} else {
    header("Location: ../registro.php?error=Todos los campos son obligatorios");
    exit();
}

$conn->close();
?>