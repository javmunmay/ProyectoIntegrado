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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #2a3d74 !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #2a3d74 !important;
            font-weight: bold;
        }

        .nav-link:hover {
            color: #1E3A5F !important;
        }

        .image-scroll-container {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            padding: 20px 0;
        }

        .image-scroll {
            display: inline-block;
            animation: scroll 30s linear infinite;
        }

        .image-scroll:hover {
            animation-play-state: paused;
        }

        .image-card {
            display: inline-block;
            margin: 0 15px;
            text-align: center;
            position: relative;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            width: 200px;
        }

        .image-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .image-card:hover img {
            transform: scale(1.05);
        }

        .like-dislike-buttons {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-like, .btn-dislike {
            background: none;
            border: none;
            padding: 0;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-like:hover {
            transform: scale(1.2);
            color: #28a745;
        }

        .btn-dislike:hover {
            transform: scale(1.2);
            color: #dc3545;
        }

        .likes-count {
            margin-top: 5px;
            font-size: 0.9rem;
            color: #2a3d74;
        }

        @keyframes scroll {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }

        .hero-section {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }

        .carousel-item {
            height: 100vh;
        }

        .carousel-item img {
            height: 100%;
            width: 100%;
            object-fit: cover;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .hero-content p {
            font-size: 1.5rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .btn-primary {
            background-color: #2a3d74;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #1E3A5F;
        }

        .carousel-control-prev,
        .carousel-control-next {
            z-index: 3;
        }

        /* Overlay oscuro para mejorar la legibilidad del texto */
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .btn-like,
        .btn-dislike {
            background: none;
            border: none;
            padding: 0;
            margin: 5px;
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-like:hover {
            transform: scale(1.2);
            color: #28a745;
        }

        .btn-dislike:hover {
            transform: scale(1.2);
            color: #dc3545;
        }

        .footer {
            background-color: #1E3A5F;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }

        .categoria-titulo {
            color: #2a3d74;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Menú de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Rally Fotográfico</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#imagenes">Imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sobre-nosotros">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ayuda">Ayuda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inicioSesion.php">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
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
            <p data-aos="fade-up" data-aos-delay="200">Descubre imágenes impresionantes de Rallys que suben nuestros usuarios y vota por tu favorita. <br><br> Sube tu fotografía ahora y únete al mundo Rally.</p>
            <a href="inicioSesion.php" class="btn btn-primary" data-aos="fade-up" data-aos-delay="400">Únete Ahora</a>
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