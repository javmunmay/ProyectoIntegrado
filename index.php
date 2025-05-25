<?php
session_start();
require_once 'php/conexion.php';

// Consulta para obtener las bases del concurso
$sql_bases = "SELECT 
                fecha_inicio_concurso, 
                fecha_fin_concurso,
                max_imagenes_por_usuario,
                extensiones_permitidas,
                tamano_maximo_mb
              FROM bases_concurso 
              ORDER BY id DESC 
              LIMIT 1";
$result_bases = $conn->query($sql_bases);
$bases_concurso = $result_bases->fetch_assoc();

// Si no hay bases configuradas, usamos valores por defecto
if (!$bases_concurso) {
    $bases_concurso = [
        'fecha_inicio_concurso' => '2025-06-01',
        'fecha_fin_concurso' => '2025-08-31',
        'max_imagenes_por_usuario' => 5,
        'extensiones_permitidas' => 'jpg, jpeg, png',
        'tamano_maximo_mb' => 10
    ];
}
?>

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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            background: linear-gradient(90deg, #3498db, rgb(219, 48, 48));
        }

        .logo {
            transition: transform 0.3s;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        /* Estilos para la sección de bases */
        .list-group-item {
            border-left: 0;
            border-right: 0;
            padding: 1rem 0;
        }

        .list-group-item:first-child {
            border-top: 0;
        }

        .list-group-item:last-child {
            border-bottom: 0;
        }

        #bases-concurso h4 {
            color: #2c3e50;
            font-weight: 600;
        }

        #bases-concurso hr {
            margin: 1.5rem 0;
            opacity: 0.2;
        }

        .icon-square {
            width: 70px;
            height: 70px;
        }

        .bg-purple {
            background-color: #6f42c1;
        }

        .text-purple {
            color: #6f42c1;
        }

        /* Mejoras para las tarjetas */
        .card-header {
            font-size: 1.25rem;
            background-color: #090643;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(0, 0, 0, 0.03);
            color: inherit;
        }

        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, 0.125);
        }

        /* Efecto hover para las tarjetas */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .icon-square {
                width: 60px;
                height: 60px;
            }

            .card-header {
                font-size: 1.1rem;
            }
        }

        .btn-registrarse {
            background-color: #090643;
            color: white;
            padding: 7px;
        }

        .btn-registrarse:hover {
            background-color: rgb(12, 8, 89);
            color: white;
        }

        .btn-iniciosesion {
            background-color: white;
            border: solid 1px #090643;
            color: #090643;
            padding: 7px;
        }

        .btn-iniciosesion:hover {
            background-color: #090643;
            color: white;
        }
    </style>
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
</head>

<body>
    <?php include 'php/nav.php'; ?>


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
            <p>Captura momentos extraordinarios y comparte tu visión del mundo. <br><br> Participa por increíbles
                premios y exposición internacional.</p>
            <a href="InicioSesion/registro.php" class="btn text-white" style="background-color: #090643;">Participa
                Ahora</a>
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

    <section id="bases-concurso" class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="text-center mb-5 categoria-titulo">Bases del Concurso</h2>

                <!-- Tarjeta de período del concurso con diseño mejorado -->
                <div class="card mb-5 border-0 shadow-lg">
                    <div class="card-header">
                        <h4 class="mb-0 text-white"><i class="bi bi-calendar-range me-2"></i> Periodo del Concurso</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="p-4 bg-light rounded">
                                    <i class="bi bi-calendar-event fs-1 text-primary mb-3"></i>
                                    <h5 class="fw-bold">Fecha de Inicio</h5>
                                    <p class="fs-5 mb-0">
                                        <?php echo date('d M Y', strtotime($bases_concurso['fecha_inicio_concurso'])); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded">
                                    <i class="bi bi-calendar-check fs-1 text-danger mb-3"></i>
                                    <h5 class="fw-bold">Fecha de Cierre</h5>
                                    <p class="fs-5 mb-0">
                                        <?php echo date('d M Y', strtotime($bases_concurso['fecha_fin_concurso'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de requisitos técnicos con iconos y diseño mejorado -->
                <div class="card mb-5 border-0 shadow-lg">
                    <div class="card-header">
                        <h4 class="mb-0 text-white"><i class="bi bi-gear me-2"></i> Requisitos Técnicos</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <div class="text-center p-3 h-100">
                                    <div
                                        class="icon-square bg-success bg-opacity-10 text-success rounded-circle mb-3 p-3 d-inline-flex align-items-center justify-content-center">
                                        <i class="bi bi-images fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold">Imágenes por usuario</h5>
                                    <p class="mb-0">Máximo <span
                                            class="badge bg-success"><?php echo $bases_concurso['max_imagenes_por_usuario']; ?></span>
                                        imágenes</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center p-3 h-100">
                                    <div
                                        class="icon-square bg-info bg-opacity-10 text-info rounded-circle mb-3 p-3 d-inline-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-image fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold">Formatos aceptados</h5>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <?php
                                        $formatos = explode(',', $bases_concurso['extensiones_permitidas']);
                                        foreach ($formatos as $formato):
                                            ?>
                                            <span
                                                class="badge bg-info text-dark"><?php echo strtoupper(trim($formato)); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-4">
                                <div class="text-center p-3 h-100">
                                    <div
                                        class="icon-square bg-warning bg-opacity-10 text-warning rounded-circle mb-3 p-3 d-inline-flex align-items-center justify-content-center">
                                        <i class="bi bi-hdd fs-2"></i>
                                    </div>
                                    <h5 class="fw-bold">Tamaño máximo</h5>
                                    <p class="mb-0"><span
                                            class="badge bg-warning text-dark"><?php echo $bases_concurso['tamano_maximo_mb']; ?>
                                            MB</span> por imagen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de sistema de votación mejorada -->
                <div class="card mb-5 border-0 shadow-lg">
                    <div class="card-header">
                        <h4 class="mb-0 text-white"><i class="bi bi-hand-thumbs-up me-2"></i> Sistema de Votación</h4>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <div class="display-3 fw-bold text-purple" style="color: #090643;">
                                    <p>∞</p>
                                </div>
                                <p class="fs-5 mb-0">votos por usuario</p>
                            </div>
                            <div class="col-md-8">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Cada
                                        usuario registrado puede votar</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Los votos
                                        pueden distribuirse libremente</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> No se
                                        permite votar por la misma foto múltiples veces</li>
                                    <li><i class="bi bi-check-circle-fill text-success me-2"></i> El sistema detecta y
                                        previene votos fraudulentos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de criterios de evaluación con diseño de acordeón -->
                <div class="card mb-5 border-0 shadow-lg">
                    <div class="card-header">
                        <h4 class="mb-0 text-white"><i class="bi bi-clipboard-check me-2"></i> Criterios de Evaluación
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="criteriosAccordion">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="bi bi-lightbulb me-2 text-warning"></i> Originalidad y Creatividad
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#criteriosAccordion">
                                    <div class="accordion-body">
                                        <p>Se valorará especialmente la capacidad de mostrar perspectivas únicas, ideas
                                            innovadoras y enfoques creativos que destaquen sobre las propuestas
                                            convencionales.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                        <i class="bi bi-grid-3x3-gap me-2 text-primary"></i> Composición y Técnica
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                    data-bs-parent="#criteriosAccordion">
                                    <div class="accordion-body">
                                        <p>Evaluación del dominio técnico en aspectos como enfoque, exposición, balance
                                            de blancos, así como la aplicación efectiva de principios de composición
                                            fotográfica.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        <i class="bi bi-heart-fill me-2 text-danger"></i> Impacto Emocional
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse"
                                    aria-labelledby="headingThree" data-bs-parent="#criteriosAccordion">
                                    <div class="accordion-body">
                                        <p>La capacidad de la fotografía para transmitir emociones, contar historias o
                                            provocar una reacción en el espectador será un factor clave en la
                                            evaluación.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                        <i class="bi bi-tag-fill me-2 text-success"></i> Adecuación a la Categoría
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                                    data-bs-parent="#criteriosAccordion">
                                    <div class="accordion-body">
                                        <p>La fotografía debe ajustarse claramente a la categoría seleccionada y cumplir
                                            con los requisitos específicos establecidos para cada una de ellas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Llamada a la acción -->
                <div class="text-center mt-5">
                    <div class="card bg-light border-0 py-4">
                        <div class="card-body">
                            <h3 class="mb-4">¿Listo para participar?</h3>
                            <p class="lead mb-4">Regístrate ahora y sube tus mejores fotografías para tener la
                                oportunidad de ganar increíbles premios.</p>
                            <a href="InicioSesion/registro.php" class="btn btn-lg px-4 text-white"
                                style="background-color: #090643;">
                                <i class="bi bi-camera-fill me-2"></i> Participar en el Concurso
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>