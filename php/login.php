<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers de seguridad básicos
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'conexion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar campos recibidos
    if (empty($_POST['correo']) || empty($_POST['contrasena'])) {
        header("Location: ../InicioSesion/inicioSesion.php?error=1"); // 1 = Campos vacíos
        exit();
    }

    // Sanitizar datos
    $correo = trim($conn->real_escape_string($_POST['correo']));
    $contrasena = $_POST['contrasena'];

    // Buscar usuario
    $sql = "SELECT id, nombre, correo, contrasena, admin, foto_perfil 
            FROM Usuarios 
            WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        header("Location: ../InicioSesion/inicioSesion.php?error=2"); // 2 = Error del sistema
        exit();
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña (compatible con hash y texto plano)
        if (password_verify($contrasena, $user['contrasena']) || $contrasena === $user['contrasena']) {
            // Actualizar a hash si estaba en texto plano
            if ($contrasena === $user['contrasena']) {
                $new_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $update_sql = "UPDATE Usuarios SET contrasena = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_hash, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
            }

            // Establecer sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nombre'] = $user['nombre'];
            $_SESSION['user_correo'] = $user['correo'];
            $_SESSION['user_admin'] = $user['admin'];
            $_SESSION['user_foto'] = !empty($user['foto_perfil']) ? $user['foto_perfil'] : '../../assets/Usuario.jpg';
            
            // Redirigir según rol
            if ($user['admin'] == 1) {
                header("Location: ../InicioSesion/admin/dashboard.php");
            } else {
                header("Location: ../InicioSesion/usuario/home.php");
            }
            exit();
        }
    }

    // Credenciales incorrectas
    header("Location: ../InicioSesion/inicioSesion.php?error=3"); // 3 = Credenciales inválidas
    exit();
} else {
    // Método no permitido
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}
?>