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
header("Location: ../login.php?mensaje=sesion_cerrada");
exit();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cierre de sesión - Rally Fotográfico</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f5f5f5;
      text-align: center;
      padding: 50px;
    }
    .spinner {
      border: 5px solid #f3f3f3;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <h1>Cerrando sesión del Rally Fotográfico...</h1>
  <div class="spinner"></div>
  <p>Serás redirigido automáticamente</p>

  <script>
    // Limpiar almacenamiento local específico de la aplicación
    localStorage.removeItem("ultima_imagen_subida");
    localStorage.removeItem("filtros_preferidos");
    
    // Redirigir después de 2 segundos (como fallback)
    setTimeout(function() {
      window.location.href = "../index.php";
    }, 2000);
  </script>
</body>
</html>