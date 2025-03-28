<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");


require_once 'conexion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar campos recibidos
    if (empty($_POST['correo']) || empty($_POST['contrasena'])) {
        header("Location: ../InicioSesion/inicioSesion.php?error=campos_vacios");
        exit();
    }

    // Sanitizar y obtener datos del formulario
    $correo = trim($conn->real_escape_string($_POST['correo']));
    $contrasena = $_POST['contrasena'];

    // Buscar el usuario por correo
    $sql = "SELECT id, nombre, correo, contrasena, usuario, admin, politica_privacidad, foto_perfil 
            FROM Usuarios 
            WHERE correo = ? AND estado = 'activo'";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        header("Location: ../InicioSesion/inicioSesion.php?error=error_bd");
        exit();
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificar la contraseña (tanto hash como texto plano para migración)
        if (password_verify($contrasena, $user['contrasena'])) {
            // Contraseña válida (hash)
            $login_valido = true;
        } elseif ($contrasena === $user['contrasena']) {
            // Contraseña válida (texto plano - para migración)
            $login_valido = true;
            
            // Actualizar a contraseña hasheada
            $new_hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $update_sql = "UPDATE Usuarios SET contrasena = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_hash, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            // Contraseña inválida
            $login_valido = false;
        }

        if ($login_valido) {
            // Establecer variables de sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_correo'] = $user['correo'];
            $_SESSION['user_admin'] = $user['admin'];
            $_SESSION['user_foto'] = $user['foto_perfil'] ?? 'assets/perfil-default.jpg';
            $_SESSION['user_politica'] = $user['politica_privacidad'];
            
            // Regenerar ID de sesión para prevenir fixation
            session_regenerate_id(true);
            
            // Redirigir según rol
            if ($user['admin'] == 1) {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../usuario/home.php");
            }
            exit();
        } else {
            header("Location: ../InicioSesion/inicioSesion.php?error=credenciales_invalidas");
            exit();
        }
    } else {
        header("Location: ../InicioSesion/inicioSesion.php?error=usuario_no_encontrado");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Método no permitido
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}
?>