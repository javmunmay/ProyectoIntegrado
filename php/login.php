<?php
session_start();
require_once 'conexion.php';

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Prevenir inyecciones SQL
    $correo = $conn->real_escape_string($correo);

    // Buscar el usuario por correo
    $sql = "SELECT * FROM Usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obtener los datos del usuario
        $user = $result->fetch_assoc();
        $hashed_password = $user['contrasena']; // Contraseña cifrada o sin cifrar en la base de datos

        // Verificar la contraseña
        if (password_verify($contrasena, $hashed_password)) {
            // Login exitoso con contraseña cifrada
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['correo'] = $user['correo'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['admin'] = $user['admin'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['foto_perfil'] = $user['foto_perfil'];

            // Redirigir según el rol del usuario
            if ($user['admin'] == 1) {
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                // Usuario normal
                header("Location: ../usuario/home.php");
                exit();
            }
        } elseif ($contrasena == $hashed_password) {
            // Si la contraseña está sin cifrar, es correcta, y se debe cifrar ahora
            $new_hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $update_sql = "UPDATE Usuarios SET contrasena = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_hashed_password, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Login exitoso después de actualizar la contraseña
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['correo'] = $user['correo'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['admin'] = $user['admin'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['foto_perfil'] = $user['foto_perfil'];

            // Redirigir según el rol del usuario
            if ($user['admin'] == 1) {
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                header("Location: ../usuario/home.php");
                exit();
            }
        } else {
            // Contraseña incorrecta
            header("Location: ../login.php?error=contrasena_incorrecta");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: ../login.php?error=usuario_no_encontrado");
        exit();
    }

    // Cerrar la conexión a la base de datos
    $stmt->close();
    $conn->close();
}
?>