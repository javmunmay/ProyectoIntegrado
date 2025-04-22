<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$nombre = $_POST['nombre'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$biografia = $_POST['bio'] ?? '';


// Procesar la foto de perfil
$foto_perfil = null;
if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {
    $nombre_archivo = $_FILES['fotoPerfil']['name'];
    $tipo_archivo = $_FILES['fotoPerfil']['type'];
    $tamano_archivo = $_FILES['fotoPerfil']['size'];
    $temp_archivo = $_FILES['fotoPerfil']['tmp_name'];
    
    // Validar que sea una imagen
    $permitidos = array("image/jpeg", "image/png", "image/gif");
    if (in_array($tipo_archivo, $permitidos)) {
        // Mover el archivo a la carpeta de perfiles
        $ruta_perfil = '../assets/perfiles/' . $usuario_id . '_' . time() . '_' . $nombre_archivo;
        if (move_uploaded_file($temp_archivo, $ruta_perfil)) {
            $foto_perfil = $ruta_perfil;
            
            // Actualizar la sesión con la nueva foto
            $_SESSION['user_foto'] = $ruta_perfil;
        }
    }
}

// Actualizar el perfil en la base de datos
if (actualizarPerfil($conn, $usuario_id, $nombre, $ubicacion, $biografia, $foto_perfil)) {
    // Actualizar el nombre en la sesión si cambió
    $_SESSION['user_nombre'] = $nombre;
    
    header("Location: ../InicioSesion/usuario/miPerfil.php?success=1");
} else {
    header("Location: ../InicioSesion/usuario/miPerfil.php?error=1");
}
exit();
?>