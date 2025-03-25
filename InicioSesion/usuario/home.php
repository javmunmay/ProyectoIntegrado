<?php
// Verificar si la sesión ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
  session_start(); // Iniciar la sesión solo si no está ya iniciada
}

// Configuraciones para deshabilitar el caché
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
  // Redirigir al login si no está autenticado
  header("Location: InicioSesion.php");
  exit();
}

// Conectar a la base de datos
include '../php/conexion.php';

// Obtener el ID del usuario desde la sesión
$user_id = $_SESSION['user_id'];

// Consulta para obtener la información del usuario
$sql = "SELECT nombre FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Guardar el nombre del usuario en una variable
$nombre_usuario = $row['nombre'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi Academia - Estudiante Programador</title>
  <link rel="stylesheet" href="../css/miAcademia.css" />


  <link rel="icon" type="image/png" href="../imagenes/favicon.ico" />
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
  <header>
    <nav>
      <img
        src="../imagenes/LogoFondoAzul.jpg"
        alt="Logo Estudiante Programador"
        class="logo" />
      <button class="menu-toggle" aria-label="Abrir menú">
        <span></span>
        <span></span>
        <span></span>
      </button>
      <ul class="nav-links">
        <li><a href="MiAcademia.php">Inicio</a></li>
        <li>
          <a href="InformacionPersonal.php">Mi Perfil</a>
        </li>
        <li><a href="CursosDentroSesion.php">Cursos</a></li>
        <li><a href="MisCertificados.php">Mis Certificados</a></li>
        <li><a href="ContactoSesionIniciada.php">Ayuda</a></li>
        <li>
          <a class="cerrarSesion" href="../php/logout.php">Cerrar Sesión</a>
        </li>
      </ul>
    </nav>
  </header>


  <!-- Contenedor para el mensaje de bienvenida -->
  <main class="container-fluid">
    <div id="welcome-popup" class="welcome-popup">
      <div class="popup-content">
        <span id="close-popup" class="close">&times;</span>
        <h2>Bienvenido a tu Academia</h2>
        <p>Para salir pulse <br> <b>"Mi Pefil" > "Cerrar Sesión"</b></p>
      </div>
    </div>

    <section class="hero-banner bg-light p-2 mb-4 mt-4 text-center">
      <h1 class="text-center mb-4 mt-4" style="font-weight: bold; color: #090643;">Mi Academia</h1>
    </section>

    <section class="hero-banner bg-light p-5 mb-4 d-flex align-items-center">
      <div class="container">
        <div class="row justify-content-center align-items-center">
          <div class="col-md-8 text-center">
            <h1 style="font-weight: bold; color: #090643;">Bienvenido</h1>


            <div id="welcome-message" style="font-weight: bold; color: #090643; transform: scale(1.15);">
              <?php echo htmlspecialchars($nombre_usuario); ?>
            </div>


            <h1 class="mt-3">
              <p class="lead mt-2" style="font-weight: bold; color: rgb(119, 119, 119);">
                Desarrolla tus habilidades al siguiente nivel.
              </p>
            </h1>
            <div class="mt-3 text-center">
              <a href="CursosDentroSesion.php" class="btn btn-primary btn-lg" style="background-color: #090643; border-color: #090643;">
                Todos los cursos
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>



    <section class="container hero-banner bg-light p-5 mb-4 pt-5 d-flex align-items-center position-relative overflow-hidden">
      <!-- Carrusel de imágenes (fondo) -->
      <div id="heroCarousel" class="carousel slide position-absolute w-100 h-100" data-bs-interval="3000" data-bs-ride="carousel" style="top: 0; left: 0; z-index: 0;">
        <div class="carousel-inner h-100">
          <!-- Imagen 1 -->
          <div class="carousel-item active h-100">
            <img src="../imagenes/CursoIntroduccionProgramacionBanner.jpg" class="d-block w-100 h-100" alt="Imagen 1" style="object-fit: cover;">
          </div>
          <!-- Imagen 2 -->
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoAngularBanner.jpg" class="d-block w-100 h-100" alt="Imagen 2" style="object-fit: cover;">
          </div>
          <!-- Imagen 3 -->
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoFundamentosJSBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoIABanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoCiberSeguridadBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoHtmlBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoPythonBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoCSSBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoLinuxBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoSqlBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
          <div class="carousel-item h-100">
            <img src="../imagenes/CursoPhpFundamentosBanner.jpg" class="d-block w-100 h-100" alt="Imagen 3" style="object-fit: cover;">
          </div>
        </div>
      </div>

      <!-- Contenido centrado -->
      <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center align-items-center">
          <div class="col-md-8 text-center" style="background: rgba(248,249,251, 0.6); padding: 10px; border-radius: 20px;">
            <h1 style="font-weight: bold; color: #090643;">+40 Cursos</h1>

            <div id="welcome-message" style="font-weight: bold; color: #090643; transform: scale(1.15);">
              <?php echo htmlspecialchars($nombre_usuario); ?>
            </div>

            <h1 class="mt-3">
              <p class="lead mt-2" style="font-weight: bold; color: rgb(70, 70, 70);">
                Completa lecciones y finaliza los cursos para subir tu
                nivel formativo y obtener certificados que validen lo aprendido.
              </p>
            </h1>
            <div class="mt-3 text-center">
              <a href="MisCertificados.php" class="btn btn-primary btn-lg" style="background-color: #090643; border-color: #090643;">
                Mis certificados
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>


    <section class="hero-banner bg-light p-5 mb-4 text-center">
      <div class="container">
        <div class="row align-items-center"> <!-- Fila para organizar el contenido -->
          <!-- Columna izquierda (Título y botones) -->

          <h1>
            <span style="font-weight: bold; color: #090643;">
              Prueba suerte: empieza un curso aleatorio 🎲
            </span>

            <p class="lead" style="font-weight: bold; color: rgb(119, 119, 119);">
              <br> ¿No sabes por dónde empezar? <br><br>¡Deja que la suerte decida por ti! 🍀
            </p>
          </h1>
          <div class="mt-3">
            <a href="../php/cursoAleatorio.php" class="btn btn-primary btn-lg" style="background-color: #090643; border-color: #090643;">
              Comenzar curso aleatorio
            </a>
          </div>
        </div>
      </div>
    </section>

  </main>

  <?php include '../php/footerSesion.php'; ?>
  <script src="../js/AppMiAcademia.js?v=1.0"></script>

  <!-- Bootstrap JS y dependencias -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>




<script>
  //Desabilita el botón de atrás del navegador

  window.onload = function() {
    history.pushState(null, null, location.href);
    window.onpopstate = function() {
      history.go(1);
    };
  };
</script>

<script>
  document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
  });
</script>

</html>