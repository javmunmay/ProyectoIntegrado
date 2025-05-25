<?php
session_start();
require_once '../../php/conexion.php';
require_once '../../php/funciones.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: https://41183897.servicio-online.net/InicioSesion/inicioSesion.php");
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

// Obtener información del concurso activo
$sql_concurso = "SELECT max_imagenes_por_usuario FROM bases_concurso ORDER BY id DESC LIMIT 1";
$result_concurso = $conn->query($sql_concurso);
$concurso = $result_concurso->fetch_assoc();
$max_imagenes_concurso = $concurso['max_imagenes_por_usuario'] ?? 5;
$imagenes_restantes = max(0, $max_imagenes_concurso - $totalImagenes);

// Verificar si se ha excedido el límite
$excede_limite = $totalImagenes > $max_imagenes_concurso;
$imagenes_a_eliminar = $excede_limite ? $totalImagenes - $max_imagenes_concurso : 0;

// Obtener todas las imágenes del usuario para el modal de eliminación
$imagenes_usuario = [];
if ($excede_limite) {
    $sql_imagenes = "SELECT id, ruta, titulo FROM imagenes WHERE usuario_id = ? AND estado IN ('activo', 'pendiente') ORDER BY fecha_subida DESC";
    $stmt = $conn->prepare($sql_imagenes);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $imagenes_usuario = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Formatear fechas
$fechaRegistro = date("d/m/Y", strtotime($datosUsuario['fecha_registro']));
$ultimoLogin = !empty($datosUsuario['ultimo_login']) ? date("d/m/Y H:i", strtotime($datosUsuario['ultimo_login'])) : "Nunca";

$conn->close();
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
            --primary-color: #090643;
            --secondary-color: #1E3A5F;
            --accent-color: #4e73df;
        }

        .profile-header {
            background: var(--primary-color);
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

        .btn-outline-danger {
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            border-left: 3px solid var(--primary-color);
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

        .card-title {
            font-weight: bold;
            color: #090643;
        }

        .btnSubir {
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
<<<<<<< HEAD

=======
        
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
        /* Estilos para el modal de advertencia */
        .modal-advertencia .modal-header {
            background-color: #dc3545;
            color: white;
        }

        .imagen-eliminar {
            position: relative;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            transition: all 0.3s;
        }

        .imagen-eliminar img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 3px;
        }

        .imagen-eliminar .form-check {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .imagen-eliminar .titulo {
            margin-top: 5px;
            font-weight: 500;
            text-align: center;
        }

        .contador-eliminar {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .imagen-seleccionada {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .btn-subir-deshabilitado {
            opacity: 0.5;
            pointer-events: none;
        }
<<<<<<< HEAD

        .btn-outline-gestionar {
            background-color: #090643;
            color: white;
        }
=======
<<<<<<< HEAD

        .btn-outline-gestionar{
            background-color: #090643;
            color: white;
        }
=======
=======
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
<<<<<<< HEAD
            <a href="../../php/subir_imagen.php" class="btnSubir  ms-auto me-3">
=======
<<<<<<< HEAD
            <a href="../../php/subir_imagen.php" class="btnSubir  ms-auto me-3">
=======
<<<<<<< HEAD
             <a href="../../php/subir_imagen.php" class="btnSubir <?php echo $excede_limite ? 'btn-subir-deshabilitado' : ''; ?>">
=======
             <a href="../../php/subir_imagen.php" class="btnSubir  ms-auto me-3">
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
                <i class="bi bi-cloud-arrow-up"></i> Subir
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="miPerfil.php">Mi Perfil</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="misImagenes.php">Mis Imágenes</a>
                    </li>
                    <li class="nav-item">
<<<<<<< HEAD
                        <a class="nav-link <?php echo $excede_limite ? 'disabled' : ''; ?>"
                            href="votacion.php">Votación</a>
=======
<<<<<<< HEAD
                        <a class="nav-link <?php echo $excede_limite ? 'disabled' : ''; ?>" href="votacion.php">Votación</a>
=======
<<<<<<< HEAD
                        <a class="nav-link <?php echo $excede_limite ? 'disabled' : ''; ?>" href="votacion.php">Votación</a>
=======
                        <a class="nav-link" href="votacion.php">Votación</a>
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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

    <!-- Modal de advertencia cuando se excede el límite -->
    <?php if ($excede_limite): ?>
        <div class="modal fade modal-advertencia" id="advertenciaModal" tabindex="-1" aria-hidden="false"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Límite de imágenes excedido
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <h5>Has excedido el límite de imágenes permitidas para el concurso.</h5>
                            <p class="mb-2">Actualmente tienes <strong><?php echo $totalImagenes; ?> imágenes</strong>
                                (límite: <?php echo $max_imagenes_concurso; ?>).</p>
                            <p class="mb-0">Debes eliminar al menos <span
                                    class="contador-eliminar"><?php echo $imagenes_a_eliminar; ?></span> imagen(es) para
                                poder continuar.</p>
                        </div>

                        <h5 class="mt-4 mb-3">Selecciona las imágenes a eliminar:</h5>
                        <form id="formEliminarImagenes" action="../../php/eliminar_imagenes.php" method="POST">
                            <div class="row">
                                <?php foreach ($imagenes_usuario as $imagen): ?>
                                    <div class="col-md-4">
                                        <div class="imagen-eliminar">
                                            <div class="form-check">
                                                <input class="form-check-input checkbox-eliminar" type="checkbox"
                                                    name="imagenes_eliminar[]" value="<?php echo $imagen['id']; ?>"
                                                    id="img-<?php echo $imagen['id']; ?>">
                                            </div>
                                            <img src="../../<?php echo htmlspecialchars($imagen['ruta']); ?>" class="img-fluid"
                                                alt="<?php echo htmlspecialchars($imagen['titulo']); ?>">
                                            <div class="titulo"><?php echo htmlspecialchars($imagen['titulo']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="imagenes_a_eliminar" value="<?php echo $imagenes_a_eliminar; ?>">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" form="formEliminarImagenes" class="btn btn-danger"
                            id="btnEliminarSeleccionadas" disabled>
                            <i class="bi bi-trash"></i> Eliminar seleccionadas
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-4 mb-5">
        <!-- Header del perfil -->
        <div class="card mb-4">
            <div class="profile-header"></div>
            <div class="profile-img-container">
                <img src="/../../assets/<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de perfil"
                    class="profile-img">
                <button class="btn btn-primary edit-profile-btn" data-bs-toggle="modal"
                    data-bs-target="#editarPerfilModal" <?php echo $excede_limite ? 'disabled' : ''; ?>>
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
                    data-bs-target="#editarPerfilModal" <?php echo $excede_limite ? 'disabled' : ''; ?>>
=======
                    data-bs-target="#editarPerfilModal">
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
                    <i class="bi bi-pencil"></i> Editar
                </button>
            </div>
            <div class="card-body text-center pt-4">
                <h2 class="card-title mb-1"><?php echo htmlspecialchars($nombreUsuario); ?></h2>

                <?php if (!empty($datosUsuario['ubicacion'])): ?>
                    <li class="list-group-item px-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        <strong>Ubicación:</strong> <?php echo htmlspecialchars($datosUsuario['ubicacion']); ?>
                    </li>
                <?php endif; ?>

                <p class="text-muted mb-3">
                    @<?php echo htmlspecialchars(strtolower(str_replace(' ', '', $nombreUsuario))); ?></p>

                <p class="card-text">
                    <?php echo htmlspecialchars($datosUsuario['biografia'] ?? 'Este usuario no ha añadido una biografía todavía.'); ?>
                </p>
            </div>
        </div>

        <!-- Mensaje de advertencia si se excede el límite -->
        <?php if ($excede_limite): ?>
            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle-fill"></i> Has excedido el límite de imágenes</h5>
                <p class="mb-0">Tienes <?php echo $totalImagenes; ?> imágenes (límite:
                    <?php echo $max_imagenes_concurso; ?>).
                    Debes eliminar <?php echo $imagenes_a_eliminar; ?> imagen(es) para poder continuar participando en el
                    concurso.
                </p>
            </div>
        <?php endif; ?>

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
                                    <div class="stat-number">
                                        <?php echo isset($estadisticas['total_likes']) ? $estadisticas['total_likes'] : 0; ?>
                                    </div>
                                    <div class="stat-label">Likes</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de imágenes disponibles -->
                        <div class="mb-3">
<<<<<<< HEAD
                            <div
                                class="stats-card text-center <?php echo $excede_limite ? 'bg-danger text-white' : ''; ?>">
                                <div class="stat-number"><?php echo $imagenes_restantes; ?></div>
                                <div class="stat-label">Imágenes disponibles</div>
                                <small class="<?php echo $excede_limite ? 'text-white' : 'text-muted'; ?>">(Límite:
                                    <?php echo $max_imagenes_concurso; ?> por concurso)</small>
                            </div>
=======
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
                            <div class="stats-card text-center <?php echo $excede_limite ? 'bg-danger text-white' : ''; ?>">
                                <div class="stat-number"><?php echo $imagenes_restantes; ?></div>
                                <div class="stat-label">Imágenes disponibles</div>
                                <small class="<?php echo $excede_limite ? 'text-white' : 'text-muted'; ?>">(Límite: <?php echo $max_imagenes_concurso; ?> por concurso)</small>
<<<<<<< HEAD
                            </div>
=======
                            </div>
=======
                            <h6 class="mb-2">Espacio utilizado</h6>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar"
                                    style="width: <?php echo min($estadisticas['espacio_utilizado'], 100); ?>%"
                                    aria-valuenow="<?php echo $estadisticas['espacio_utilizado']; ?>" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted"><?php echo $estadisticas['espacio_utilizado']; ?>% de 1GB
                                usado</small>
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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
                        <a href="misImagenes.php" class="btn btn-sm btn-outline-gestionar">Gestionar</a>
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
                                <a href="../../php/subir_imagen.php" class="btn btn-primary btn-sm">Subir primera imagen</a>
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
                            <p class="small text-muted mb-1">Tu imagen "Atardecer en la playa" recibió 15 nuevos likes
                            </p>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPerfilModalLabel">Editar perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarPerfil" action="../../php/actualizar_perfil.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img src="/../../assets/<?php echo htmlspecialchars($fotoPerfil); ?>" id="previewFoto"
                                class="rounded-circle mb-2" width="125" height="125">
                            <input type="file" class="form-control d-none" id="fotoPerfil" name="fotoPerfil"
                                accept="image/*">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                onclick="document.getElementById('fotoPerfil').click()">
                                <i class="bi bi-camera"></i> Cambiar foto
                            </button>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    value="<?php echo htmlspecialchars($nombreUsuario); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ubicacion" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion"
                                    value="<?php echo htmlspecialchars($datosUsuario['ubicacion'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografía</label>
                            <textarea class="form-control" id="bio" name="bio"
                                rows="3"><?php echo htmlspecialchars($datosUsuario['biografia'] ?? ''); ?></textarea>
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
        // Mostrar modal de advertencia si se excede el límite
        <?php if ($excede_limite): ?>
            document.addEventListener('DOMContentLoaded', function () {
                var advertenciaModal = new bootstrap.Modal(document.getElementById('advertenciaModal'));
                advertenciaModal.show();

                // Variables de control
                const checkboxes = document.querySelectorAll('.checkbox-eliminar');
                const contador = document.querySelector('.contador-eliminar');
                const btnEliminar = document.getElementById('btnEliminarSeleccionadas');
                const imagenesAEliminar = <?php echo $imagenes_a_eliminar; ?>;

                // Función para actualizar el estado
                function actualizarEstado() {
                    const seleccionadas = document.querySelectorAll('.checkbox-eliminar:checked').length;
                    const restantes = Math.max(0, imagenesAEliminar - seleccionadas);

                    contador.textContent = restantes;
                    btnEliminar.disabled = restantes > 0;

                    // Resaltar imágenes seleccionadas
                    checkboxes.forEach(checkbox => {
                        const card = checkbox.closest('.imagen-eliminar');
                        if (checkbox.checked) {
                            card.classList.add('imagen-seleccionada');
                        } else {
                            card.classList.remove('imagen-seleccionada');
                        }
                    });
                }

                // Event listeners
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', actualizarEstado);
                });

                // Validar antes de enviar el formulario
                document.getElementById('formEliminarImagenes').addEventListener('submit', function (e) {
                    const seleccionadas = document.querySelectorAll('.checkbox-eliminar:checked').length;
                    if (seleccionadas < imagenesAEliminar) {
                        e.preventDefault();
                        alert(`Debes seleccionar al menos ${imagenesAEliminar} imágenes para eliminar.`);
                    }
                });
            });
        <?php endif; ?>

        // Preview de la foto de perfil al seleccionar
        document.getElementById('fotoPerfil').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    document.getElementById('previewFoto').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>