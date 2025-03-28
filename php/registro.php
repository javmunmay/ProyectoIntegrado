<?php

require_once 'conexion.php';

// Verificar si los datos requeridos están en POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar campos obligatorios
    if (!isset($_POST['nombre'], $_POST['correo'], $_POST['contrasena'], $_POST['confirmar_contrasena'], $_POST['politica'])) {
        header("Location: ../InicioSesion/registro.php?error=Todos los campos son obligatorios");
        exit();
    }

    // Verificar que las contraseñas coincidan
    if ($_POST['contrasena'] !== $_POST['confirmar_contrasena']) {
        header("Location: ../InicioSesion/registro.php?error=Las contraseñas no coinciden");
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
        header("Location: ../InicioSesion/registro.php?error=El correo ya está registrado");
        exit();
    }

    // Validar que se haya aceptado la política de privacidad
    if (!isset($_POST['politica']) || $_POST['politica'] != 'on') {
        header("Location: ../InicioSesion/registro.php?error=Debes aceptar la política de privacidad");
        exit();
    }

    // Recibir y sanitizar los datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $politica = 1; // Siempre será 1 porque es campo requerido

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
        header("Location: ../InicioSesion/inicioSesion.php?success=Registro exitoso. Por favor inicia sesión");
        exit();
    } else {
        header("Location: ../InicioSesion/registro.php?error=Error en el registro: " . urlencode($stmt->error));
        exit();
    }

    $stmt->close();
} else {
    header("Location: ../InicioSesion/registro.php?error=Método no permitido");
    exit();
}

$conn->close();
?>