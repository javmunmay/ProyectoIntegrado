<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rally Fotográfico - Vota y Comparte Fotos de Rallys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="css/stylesIndex.css">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trending.php">Trending <i class="bi bi-graph-up"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nuevosTalentos.php">Nuevos Talentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ganadoras">Top Shots</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary" href="InicioSesion/registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary" href="InicioSesion/inicioSesion.php">
                            Iniciar Sesión <i class="bi bi-key"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <section id="inicio" class="hero-section">

        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <div class="carousel-item active">
                    <img src="assets/foto1.jpeg" class="d-block w-100" alt="Foto 1">
                </div>

                <div class="carousel-item">
                    <img src="assets/foto2.jpeg" class="d-block w-100" alt="Foto 2">
                </div>

                <div class="carousel-item">
                    <img src="assets/foto3.jpg" class="d-block w-100" alt="Foto 3">
                </div>
            </div>
        </div>


        <div class="container hero-content">
            <h1 data-aos="fade-up">Rally Fotográfico</h1>
            <p data-aos="fade-up" data-aos-delay="200">Descubre imágenes impresionantes de Rallys que suben nuestros
                usuarios y vota por tu favorita. <br><br> Sube tu fotografía ahora y únete al mundo Rally.</p>
            <a href="InicioSesion/inicioSesion.php" class="btn btn-primary" data-aos="fade-up" data-aos-delay="400">Únete Ahora</a>
        </div>
    </section>


    <section id="destacadas" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Imágenes Destacadas</h2>
        <div class="image-scroll-container">
            <div class="image-scroll">
                <?php include 'php/imagenesMasVotadas.php'; ?>
            </div>
        </div>
    </section>


    <section id="imagenes" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Últimas Imágenes Aprobadas</h2>
        <div class="row">
            <?php include 'php/mostrar_fotos.php'; ?>
        </div>
    </section>


    <footer class="footer text-center">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Rally Fotográfico. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    

    
</body>

</html>