<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PixFly - Concurso de Fotografía Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            position: relative;
            height: 100vh;
            min-height: 600px;
            overflow: hidden;
        }
        
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
            width: 80%;
        }
        
        .carousel-item img {
            object-fit: cover;
            height: 100vh;
            min-height: 600px;
            filter: brightness(0.6);
        }
        
        .image-scroll-container {
            overflow-x: auto;
            white-space: nowrap;
            padding: 15px 0;
        }
        
        .image-scroll {
            display: inline-flex;
            gap: 15px;
        }
        
        .image-scroll img {
            height: 250px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .categoria-titulo {
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            padding-bottom: 10px;
        }
        
        .categoria-titulo:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #9b59b6);
        }
        
        .logo {
            transition: transform 0.3s;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
    </style>
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="assets/logo.png" alt="Logo PixFly" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tendencias.php">Tendencias <i class="bi bi-graph-up"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nuevos-fotografos.php">Nuevos Talentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ganadores">Ganadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categorias.php">Categorías</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary" href="registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary" href="inicio-sesion.php">
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
                    <img src="assets/foto1.jpg" class="d-block w-100" alt="Ganador concurso PixFly 2022">
                </div>
                <div class="carousel-item">
                    <img src="assets/foto2.jpg" class="d-block w-100" alt="Mejor fotografía paisajística">
                </div>
                <div class="carousel-item">
                    <img src="assets/foto3.jpg" class="d-block w-100" alt="Fotografía conceptual destacada">
                </div>
            </div>
        </div>

        <div class="container hero-content">
            <h1>Concurso de Fotografía PixFly 2025</h1>
            <p>Captura momentos extraordinarios y comparte tu visión del mundo. <br><br> Participa por increíbles premios y exposición internacional.</p>
            <a href="registro.php" class="btn btn-primary">Participa Ahora</a>
        </div>
    </section>

    <section id="destacadas" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Fotografías Destacadas</h2>
        <div class="image-scroll-container">
            <div class="image-scroll">
                <!-- PHP incluiría las imágenes más votadas -->
                <img src="assets/foto1.jpg" alt="Fotografía destacada 1">
                <img src="assets/foto2.jpg" alt="Fotografía destacada 2">
                <img src="assets/foto3.jpg" alt="Fotografía destacada 3">
                <img src="assets/foto4.jpg" alt="Fotografía destacada 4">
                <img src="assets/foto5.jpg" alt="Fotografía destacada 5">
            </div>
        </div>
    </section>

    <section id="categorias" class="container mt-5 mb-5">
        <h2 class="text-center mb-4 categoria-titulo">Categorías del Concurso</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/paisajes.jpg" class="card-img-top" alt="Paisajes">
                    <div class="card-body">
                        <h5 class="card-title">Paisajes</h5>
                        <p class="card-text">Captura la belleza de la naturaleza en su máxima expresión.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/retrato.jpg" class="card-img-top" alt="Retratos">
                    <div class="card-body">
                        <h5 class="card-title">Retratos</h5>
                        <p class="card-text">Expresa emociones y cuenta historias a través de rostros.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="assets/conceptual.jpg" class="card-img-top" alt="Conceptual">
                    <div class="card-body">
                        <h5 class="card-title">Fotografía Conceptual</h5>
                        <p class="card-text">Transmite ideas abstractas a través de imágenes creativas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img src="assets/logo-white.png" alt="PixFly" style="height: 40px; margin-bottom: 15px;">
                    <p>Plataforma líder en concursos de fotografía digital desde 2010.</p>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope"></i> info@pixfly.com</li>
                        <li><i class="bi bi-phone"></i> +34 123 456 789</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">© 2025 PixFly. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>