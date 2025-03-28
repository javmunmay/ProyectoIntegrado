<?php
session_start();

// Limpiar datos específicos de la sesión relacionados con el rally
unset($_SESSION['user_id']);
unset($_SESSION['correo']);
unset($_SESSION['nombre']);
unset($_SESSION['admin']);
unset($_SESSION['usuario']);
unset($_SESSION['foto_perfil']);

// Destruir completamente la sesión
session_destroy();

// Redirigir a la página de inicio con mensaje
header("Location: ../InicioSesion/inicioSesion.php?mensaje=sesion_cerrada");
exit();
?>