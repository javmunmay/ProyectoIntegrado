<?php
session_start();
require_once '../../php/conexion.php';
require_once '../../php/funciones.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: InicioSesion/inicioSesion.php");
    exit();
}

// Obtener datos del usuario
$usuario_id = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['user_nombre'];
$fotoPerfil = $_SESSION['user_foto'] ?? '../../assets/Usuario.jpg';

// Consultas para obtener información del usuario
$datosUsuario = obtenerDatosUsuario($conn, $usuario_id);
$totalImagenes = contarImagenesUsuario($conn, $usuario_id);
$imagenesRecientes = obtenerImagenesRecientes($conn, $usuario_id, 4);
$estadisticas = obtenerEstadisticasUsuario($conn, $usuario_id);

// Formatear fechas
$fechaRegistro = date("d/m/Y", strtotime($datosUsuario['fecha_registro']));
$ultimoLogin = !empty($datosUsuario['ultimo_login']) ? date("d/m/Y H:i", strtotime($datosUsuario['ultimo_login'])) : "Nunca";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - <?php echo htmlspecialchars($nombreUsuario); ?> | pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
    <style>
        :root {
            --primary-color: #2a3d74;
            --secondary-color: #1E3A5F;
            --accent-color: #4e73df;
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            height: 200px;
            border-radius: 15px 15px 0 0;
        }

        .profile-img-container {
            margin-top: -75px;
            text-align: center;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .progress-bar {
            background-color: var(--accent-color);
        }

        .activity-item {
            border-left: 3px solid var(--accent-color);
            padding-left: 15px;
            margin-bottom: 15px;
        }

        .image-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .image-thumbnail:hover {
            transform: scale(1.03);
        }

        .badge-premium {
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: #000;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            color: var(--primary-color);
            margin-right: 10px;
            transition: all 0.3s;
        }

        .social-icon:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .edit-profile-btn {
            position: absolute;
            top: 15px;
            right: 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <a href="../../php/subir_imagen.php" class="btn btn-primary ms-auto me-3">
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



    <div class="container mt-4 mb-5">
        <!-- Header del perfil -->
        <div class="card mb-4">
            <div class="profile-header"></div>
            <div class="profile-img-container">
                <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de perfil" class="profile-img">
                <button class="btn btn-primary edit-profile-btn" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                    <i class="bi bi-pencil"></i> Editar
                </button>
            </div>
            <div class="card-body text-center pt-4">
                <h2 class="card-title mb-1"><?php echo htmlspecialchars($nombreUsuario); ?></h2>
                <p class="text-muted mb-3">@<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $nombreUsuario))); ?></p>

                <p class="card-text"><?php echo htmlspecialchars($datosUsuario['biografia'] ?? 'Este usuario no ha añadido una biografía todavía.'); ?></p>
            </div>
        </div>

        <div class="row">
            <!-- Columna izquierda - Estadísticas -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Estadísticas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="stats-card text-center">
                                    <div class="stat-number"><?php echo $totalImagenes; ?></div>
                                    <div class="stat-label">Imágenes</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stats-card text-center">
                                    <div class="stat-number"><?php echo $estadisticas['total_likes']; ?></div>
                                    <div class="stat-label">Likes</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="mb-2">Espacio utilizado</h6>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo min($estadisticas['espacio_utilizado'], 100); ?>%"
                                    aria-valuenow="<?php echo $estadisticas['espacio_utilizado']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted"><?php echo $estadisticas['espacio_utilizado']; ?>% de 1GB usado</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <i class="bi bi-envelope me-2"></i>
                                <strong>Email:</strong> <?php echo htmlspecialchars($datosUsuario['correo']); ?>
                            </li>
                            <li class="list-group-item px-0">
                                <i class="bi bi-calendar-check me-2"></i>
                                <strong>Registrado:</strong> <?php echo $fechaRegistro; ?>
                            </li>
                            <li class="list-group-item px-0">
                                <i class="bi bi-clock-history me-2"></i>
                                <strong>Último login:</strong> <?php echo $ultimoLogin; ?>
                            </li>
                            <?php if (!empty($datosUsuario['ubicacion'])): ?>
                                <li class="list-group-item px-0">
                                    <i class="bi bi-geo-alt me-2"></i>
                                    <strong>Ubicación:</strong> <?php echo htmlspecialchars($datosUsuario['ubicacion']); ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Columna derecha - Contenido principal -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-images me-2"></i>Mis imágenes recientes</h5>
                        <a href="misImagenes.php" class="btn btn-sm btn-outline-primary">Ver todas</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($imagenesRecientes)): ?>
                            <div class="row">
                                <?php foreach ($imagenesRecientes as $imagen): ?>
                                    <div class="col-md-6 col-lg-3 mb-3">
                                        <a href="../../php/detalle_imagen.php?id=<?php echo $imagen['id']; ?>">
                                            <img src="<?php echo '../../' . htmlspecialchars($imagen['ruta']); ?>"
                                                alt="<?php echo htmlspecialchars($imagen['titulo']); ?>"
                                                class="image-thumbnail">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-images fs-1 text-muted"></i>
                                <p class="text-muted mt-2">No has subido imágenes todavía</p>
                                <a href="subir_imagen.php" class="btn btn-primary btn-sm">Subir primera imagen</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Actividad reciente</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-item">
                            <h6>Imagen destacada</h6>
                            <p class="small text-muted mb-1">Tu imagen "Atardecer en la playa" recibió 15 nuevos likes</p>
                            <small class="text-muted">Hace 2 días</small>
                        </div>
                        <div class="activity-item">
                            <h6>Nuevo comentario</h6>
                            <p class="small text-muted mb-1">Usuario123 comentó en tu imagen "Paisaje urbano"</p>
                            <small class="text-muted">Hace 3 días</small>
                        </div>
                        <div class="activity-item">
                            <h6>Imagen subida</h6>
                            <p class="small text-muted mb-1">Subiste "Paisaje montañoso"</p>
                            <small class="text-muted">Hace 1 semana</small>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary mt-2">Ver toda la actividad</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPerfilModalLabel">Editar perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPerfil" action="../../php/actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" id="previewFoto" class="rounded-circle mb-2" width="120" height="75">
                            <input type="file" class="form-control d-none" id="fotoPerfil" name="fotoPerfil" accept="image/*">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('fotoPerfil').click()">
                                <i class="bi bi-camera"></i> Cambiar foto
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombreUsuario); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($datosUsuario['ubicacion'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografía</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($datosUsuario['biografia'] ?? ''); ?></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="formEditarPerfil" class="btn btn-primary">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview de la foto de perfil al seleccionar
        document.getElementById('fotoPerfil').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('previewFoto').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>