<?php
session_start();
require_once '../../php/conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: https://41183897.servicio-online.net/InicioSesion/inicioSesion.php");
    exit();
}

// Obtener datos del usuario
$usuario_id = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['user_nombre'];

// Consultas a la base de datos
// 1. Número de imágenes subidas por el usuario
$sql_user_images = "SELECT COUNT(*) as total FROM imagenes WHERE usuario_id = ? AND estado = 'activo'";
$stmt_user = $conn->prepare($sql_user_images);
$stmt_user->bind_param("i", $usuario_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_images = $result_user->fetch_assoc()['total'];
$stmt_user->close();

// 2. Número total de imágenes en la plataforma
$sql_total_images = "SELECT COUNT(*) as total FROM imagenes WHERE estado = 'activo'";
$result_total = $conn->query($sql_total_images);
$total_images = $result_total->fetch_assoc()['total'];

// 3. Obtener información del concurso activo
$sql_concurso = "SELECT * FROM bases_concurso ORDER BY id DESC LIMIT 1";
$result_concurso = $conn->query($sql_concurso);
$concurso = $result_concurso->fetch_assoc();

// 4. Obtener imágenes para el feed (últimas 20 imágenes activas)
$sql_feed = "SELECT i.id, i.nombre_archivo, i.ruta, i.titulo, i.descripcion, 
             u.nombre as usuario_nombre, u.foto_perfil as usuario_foto, i.likes
             FROM imagenes i
             JOIN Usuarios u ON i.usuario_id = u.id
             WHERE i.estado = 'activo'
             ORDER BY i.fecha_subida DESC
             LIMIT 20";
$result_feed = $conn->query($sql_feed);
$feed_images = [];
if ($result_feed->num_rows > 0) {
    while ($row = $result_feed->fetch_assoc()) {
        $feed_images[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
    <style>
        :root {
            --primary-color: rgb(255, 255, 255);
            --secondary-color: rgb(71, 58, 255);
            --accent-color: rgb(252, 3, 3);
            --light-color: rgb(0, 4, 255);
            --dark-color: #343A40;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .btnSubir{
            background-color: #090643;
            padding-left: 25px;
            padding-right: 25px;
            padding-top: 12px;
            padding-bottom: 12px;
            color: white;
            border-radius: 6px;
            font-size: 19px;
            text-decoration: none;
        }

        .btn-outline-danger {
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .welcome-section {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .welcome-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-align: center;
            border: none;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-card h4 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .stats-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .stats-card .btn {
            border-radius: 20px;
            padding: 5px 20px;
            font-weight: 500;
            margin-top: 10px;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            position: relative;
            margin-bottom: 30px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 3px;
        }

        .feed-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .post-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .post-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .post-user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid var(--light-color);
        }

        .post-image {
            width: 100%;
            max-height: 600px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .post-image:hover {
            opacity: 0.9;
        }

        .post-body {
            padding: 20px;
        }

        .post-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-color);
        }

        .post-description {
            color: #666;
            margin-bottom: 0;
        }

        .post-actions {
            padding: 10px 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
        }

        .action-icon {
            font-size: 24px;
            margin-right: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: #666;
        }

        .action-icon:hover {
            transform: scale(1.1);
        }

        .bi-heart:hover,
        .bi-heart-fill {
            color: var(--accent-color) !important;
        }

        .bi-chat:hover {
            color: var(--primary-color) !important;
        }

        .bi-share:hover {
            color: var(--secondary-color) !important;
        }

        .likes-count {
            font-weight: 500;
            color: var(--dark-color);
            margin-left: 5px;
        }

        .upload-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 5px 20px rgba(108, 99, 255, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .upload-btn:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 8px 25px rgba(108, 99, 255, 0.5);
            color: white;
        }

        @media (max-width: 768px) {
            .stats-card h2 {
                font-size: 2rem;
            }

            .welcome-section {
                padding: 20px;
            }

            .upload-btn {
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }

        .nombreBienvenida{
            color: #090643;
            font-weight: bold;
        }

        .textinfo{
            color: #090643;
            font-weight: bold;
        }

        .btninfo{
            color: #090643;
            font-weight: bold;
            background-color:rgb(255, 255, 255);
            border: solid 2px #090643;
            border-radius: 30px;
            padding: 7px;
            text-decoration: none;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1rem 0;
            text-align: center;
            z-index: 1000;
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
        }
        
        /* Estilos para el modal del concurso */
        .modal-concurso .modal-header {
            background-color: #090643;
            color: white;
        }
        
        .modal-concurso .modal-body {
            padding: 20px;
        }
        
        .modal-concurso .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .modal-concurso .info-item:last-child {
            border-bottom: none;
        }
        
        .modal-concurso .info-label {
            font-weight: 600;
            color: #090643;
        }
        
        .modal-concurso .info-value {
            color: #555;
<<<<<<< HEAD
=======
=======
            /* Para que no se oculte debajo de otros elementos */
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
             <a href="../../php/subir_imagen.php" class="btnSubir  ms-auto me-3">
                <i class="bi bi-cloud-arrow-up"></i> Subir
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="miPerfil.php">Mi Perfil</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="misImagenes.php">Mis Imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger" href="../../php/logout.php">
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-3">¡Hola, <span
                            class="nombreBienvenida"><?php echo htmlspecialchars($nombreUsuario); ?></span>!</h2>
                    <p class="lead mb-0">Bienvenido a pixFly, la comunidad de fotografía más vibrante. Comparte tus
                        momentos y descubre increíbles imágenes de otros fotógrafos.</p>
                </div>
                <div class="col-md-4 text-center">
                    <img src="/../../assets/<?php echo $_SESSION['user_foto']; ?>" alt="Foto de perfil"
                        class="user-avatar rounded-circle">
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="icon-circle mb-3 mx-auto"
                        style="background-color: rgba(65, 35, 198, 0.1); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-images text-success" style="font-size: 24px;"></i>
                    </div>
                    <h4>Mis imágenes</h4>
                    <h2 class="textinfo"><?php echo $user_images; ?></h2>
                    <p class="text-muted">subidas a la plataforma</p>
                    <a href="misImagenes.php" class="btninfo btn-sm">
                        <i class="bi bi-eye me-1"></i> Ver mis imágenes
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="icon-circle mb-3 mx-auto"
                        style="background-color: rgba(33, 150, 243, 0.1); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-globe text-info" style="font-size: 24px;"></i>
                    </div>
                    <h4>Total en pixFly</h4>
                    <h2 class="textinfo"><?php echo $total_images; ?></h2>
                    <p class="text-muted">imágenes compartidas</p>
<<<<<<< HEAD
                    <a href="votacion.php" class="btninfo btn-sm">
=======
<<<<<<< HEAD
                    <a href="votacion.php" class="btninfo btn-sm">
=======
                    <a href="../votacion.php" class="btninfo btn-sm">
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
                        <i class="bi bi-grid me-1"></i> Explorar
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="icon-circle mb-3 mx-auto"
                        style="background-color: rgba(255, 152, 0, 0.1); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-trophy text-warning" style="font-size: 24px;"></i>
                    </div>
                    <h4>Concurso activo</h4>
                    <h2 class="textinfo">Jun - Ago</h2>
                    <p class="text-muted">participa y gana</p>
<<<<<<< HEAD
                    <button type="button" class="btninfo btn-sm" data-bs-toggle="modal" data-bs-target="#concursoModal">
                        <i class="bi bi-arrow-right me-1"></i> Más información
                    </button>
=======
<<<<<<< HEAD
                    <button type="button" class="btninfo btn-sm" data-bs-toggle="modal" data-bs-target="#concursoModal">
                        <i class="bi bi-arrow-right me-1"></i> Más información
                    </button>
=======
                    <a href="../votacion.php" class="btninfo btn-sm">
                        <i class="bi bi-arrow-right me-1"></i> Más información
                    </a>
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
    <!-- Modal del Concurso -->
    <div class="modal fade modal-concurso" id="concursoModal" tabindex="-1" aria-labelledby="concursoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="concursoModalLabel">Bases del Concurso Fotográfico</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="info-item">
                        <div class="info-label">Período del concurso:</div>
                        <div class="info-value">
                            <?php 
                                echo date('d/m/Y', strtotime($concurso['fecha_inicio_concurso'])) . 
                                ' - ' . date('d/m/Y', strtotime($concurso['fecha_fin_concurso']));
                            ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Imágenes permitidas por usuario:</div>
                        <div class="info-value"><?php echo $concurso['max_imagenes_por_usuario']; ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Formatos aceptados:</div>
                        <div class="info-value"><?php echo strtoupper(str_replace(',', ', ', $concurso['extensiones_permitidas'])); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Tamaño máximo por imagen:</div>
                        <div class="info-value"><?php echo $concurso['tamano_maximo_mb']; ?> MB</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Premios:</div>
                        <div class="info-value">
                            <ul>
                                <li>1er puesto: $500 + Kit fotográfico profesional</li>
                                <li>2do puesto: $300 + Curso avanzado de fotografía</li>
                                <li>3er puesto: $200 + Membresía premium por 1 año</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Temática:</div>
                        <div class="info-value">"La belleza en los detalles cotidianos"</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <a href="votacion.php" class="btn btn-primary">Participar ahora</a>
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
=======
=======


>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Image Modal
        $('#imageModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var imageSrc = button.data('image');
            var imageTitle = button.data('title');
            var imageDesc = button.data('description');

            var modal = $(this);
            modal.find('#modalImage').attr('src', imageSrc);
            modal.find('#modalImageTitle').text(imageTitle);
            modal.find('#modalImageDescription').text(imageDesc);
        });

        // Like functionality with AJAX
        $('.like-btn').click(function () {
            var icon = $(this);
            var imageId = icon.data('image-id');
            var likesCount = icon.siblings('.likes-count');
            var currentLikes = parseInt(likesCount.text());

            if (icon.hasClass('bi-heart-fill')) {
                // Unlike
                $.post('../../php/unlike_image.php', { image_id: imageId }, function (response) {
                    if (response.success) {
                        icon.removeClass('bi-heart-fill text-danger').addClass('bi-heart');
                        likesCount.text(currentLikes - 1);
                    }
                });
            } else {
                // Like
                $.post('../../php/like_image.php', { image_id: imageId }, function (response) {
                    if (response.success) {
                        icon.removeClass('bi-heart').addClass('bi-heart-fill text-danger');
                        likesCount.text(currentLikes + 1);
                    }
                });
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>