<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rally Fotográfico - Vota y Comparte Fotos de Rallys</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" type="text/css" href="css/stylesIndex.css">
</head>

<body>

    <!-- Menú de navegación -->
    <nav>
        <a href="/">
            <img src="assets/logo.png" alt="Logo Rally Fotográfico" class="logo" />
        </a>
        <button class="menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Inicio</a></li>
            <li><a href="trending.php">Trending 📈</a></li>
            <li><a href="nuevosTalentos.php">Nuevos Talentos</a></li>
            <li><a href="#ganadoras">Top Shots</a></li>
            <li><a href="contacto.php">Contacto</a></li>
            <li><a href="InicioSesion/registro.php" class="login-btn">Registrarse</a></li>
            <li><a href="InicioSesion/inicioSesion.php" class="login-btn">Iniciar Sesión 🔑</a></li>
        </ul>
    </nav>

    <!-- Hero Section con Carrusel de Fondo -->
    <section id="inicio" class="hero-section">
        <!-- Carrusel de imágenes -->
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- Imagen 1 -->
                <div class="carousel-item active">
                    <img src="assets/foto1.jpeg" class="d-block w-100" alt="Foto 1">
                </div>
                <!-- Imagen 2 -->
                <div class="carousel-item">
                    <img src="assets/foto2.jpeg" class="d-block w-100" alt="Foto 2">
                </div>
                <!-- Imagen 3 -->
                <div class="carousel-item">
                    <img src="assets/foto3.jpg" class="d-block w-100" alt="Foto 3">
                </div>
            </div>
            <!-- Controles del carrusel (opcional) -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <!-- Contenido superpuesto (texto y botón) -->
        <div class="container hero-content">
            <h1 data-aos="fade-up">Rally Fotográfico</h1>
            <p data-aos="fade-up" data-aos-delay="200">Descubre imágenes impresionantes de Rallys que suben nuestros
                usuarios y vota por tu favorita. <br><br> Sube tu fotografía ahora y únete al mundo Rally.</p>
            <a href="InicioSesion/inicioSesion.php" class="btn btn-primary" data-aos="fade-up" data-aos-delay="400">Únete Ahora</a>
        </div>
    </section>

    <!-- Sección de Imágenes Destacadas -->
    <section id="destacadas" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Imágenes Destacadas</h2>
        <div class="image-scroll-container">
            <div class="image-scroll">
                <?php include 'php/imagenesMasVotadas.php'; ?>
            </div>
        </div>
    </section>

    <!-- Sección de Imágenes -->
    <section id="imagenes" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Últimas Imágenes Aprobadas</h2>
        <div class="row">
            <?php include 'php/mostrar_fotos.php'; ?>
        </div>
    </section>

    <!-- Pie de página -->
    <footer class="footer text-center">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Rally Fotográfico. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS (Animate On Scroll) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>