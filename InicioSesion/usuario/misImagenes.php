<?php
session_start();
require_once '../../php/conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: InicioSesion/inicioSesion.php");
    exit();
}

// Obtener datos del usuario
$usuario_id = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['user_nombre'];

$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : "Usuario de prueba";

// Función para obtener imágenes del usuario según su estado
function obtenerImagenes($conn, $usuario_id, $estado) {
    $query = "SELECT * FROM imagenes WHERE usuario_id = ? AND estado = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $usuario_id, $estado);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Imágenes - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
</head>

<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
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
                        <a class="btn btn-outline-danger" href="InicioSesion/inicioSesion.php">
                            Cerrar Sesión <i class="bi bi-cross"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sección Gestionar Mis Imágenes -->
<section id="gestionar-imagenes" class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="categoria-titulo mb-0">Mis Imágenes</h2>
        <a href="subirImagen.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Subir Nueva
        </a>
    </div>
    
    <!-- Filtros y búsqueda -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar imágenes..." id="buscarImagen">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select" id="filtroOrden">
                <option value="recientes">Más recientes primero</option>
                <option value="antiguas">Más antiguas primero</option>
                <option value="titulo-asc">Por título (A-Z)</option>
                <option value="titulo-desc">Por título (Z-A)</option>
            </select>
        </div>
    </div>

    <!-- Contenedor de imágenes -->
    <div class="row g-4" id="contenedor-imagenes">
        <?php
        $imagenesActivas = obtenerImagenes($conn, $usuario_id, 'activo');
        
        if (empty($imagenesActivas)): ?>
            <div class="col-12">
                <div class="text-center py-5 bg-light rounded-3">
                    <i class="bi bi-images fs-1 text-muted"></i>
                    <h4 class="mt-3">No tienes imágenes subidas</h4>
                    <p class="text-muted">Comienza compartiendo tus fotografías con la comunidad</p>
                    <a href="subirImagen.php" class="btn btn-primary mt-2">
                        <i class="bi bi-cloud-arrow-up"></i> Subir mi primera imagen
                    </a>
                </div>
            </div>
        <?php else: 
            foreach ($imagenesActivas as $imagen): 
                $rutaImagen = '../../' . htmlspecialchars($imagen['ruta']);
                $fechaFormateada = date('d/m/Y', strtotime($imagen['fecha_subida']));
        ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="<?= $rutaImagen ?>" 
                             class="card-img-top img-fluid" 
                             alt="<?= htmlspecialchars($imagen['titulo']) ?>"
                             style="height: 250px; object-fit: cover;">
                        <span class="position-absolute top-0 end-0 bg-primary text-white px-2 py-1 m-2 rounded-pill small">
                            <i class="bi bi-heart-fill"></i> <?= $imagen['likes'] ?? 0 ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?= htmlspecialchars($imagen['titulo']) ?></h5>
                        <p class="card-text text-muted small"><?= htmlspecialchars($imagen['descripcion']) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> <?= $fechaFormateada ?>
                            </small>
                            <div class="btn-group btn-group-sm">
                                <a href="editarImagen.php?id=<?= $imagen['id'] ?>" 
                                   class="btn btn-outline-primary"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="eliminarImagen.php" method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta imagen?');">
                                    <input type="hidden" name="id" value="<?= $imagen['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <a href="detalleImagen.php?id=<?= $imagen['id'] ?>" 
                                   class="btn btn-outline-secondary"
                                   title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; 
        endif; ?>
    </div>
    </section>

    <!-- Sección Imágenes en Revisión -->
<section id="revision-imagenes" class="container mt-5">
    <h2 class="text-center mb-4 categoria-titulo">Imágenes en revisión</h2>
    <div class="row">
        <?php
        $imagenesPendientes = obtenerImagenes($conn, $usuario_id, 'pendiente');
        if (empty($imagenesPendientes)) {
            echo '<div class="col-12 text-center py-4">
                    <i class="bi bi-images fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No hay imágenes en revisión</p>
                  </div>';
        } else {
            foreach ($imagenesPendientes as $imagen) {
                echo '
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="' . '../../' . htmlspecialchars($imagen['ruta']) . '" 
                             class="card-img-top img-fluid" 
                             alt="' . htmlspecialchars($imagen['titulo']) . '"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($imagen['titulo']) . '</h5>
                            <p class="card-text text-truncate">' . htmlspecialchars($imagen['descripcion']) . '</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">' . date("d/m/Y", strtotime($imagen['fecha_subida'])) . '</small>
                                <span class="badge bg-warning text-dark">En revisión</span>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        }
        ?>
    </div>
</section>

    <!-- Sección Imágenes No Aceptadas -->
<section id="no-aceptadas-imagenes" class="container mt-5">
    <h2 class="text-center mb-4 categoria-titulo">Imágenes no aceptadas</h2>
    <div class="row">
        <?php
        $imagenesRechazadas = obtenerImagenes($conn, $usuario_id, 'inactivo');
        if (empty($imagenesRechazadas)) {
            echo '<div class="col-12 text-center py-4">
                    <i class="bi bi-x-circle fs-1 text-muted"></i>
                    <p class="text-muted mt-2">No hay imágenes rechazadas</p>
                  </div>';
        } else {
            foreach ($imagenesRechazadas as $imagen) {
                echo '
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-danger">
                        <div class="card-header bg-danger bg-opacity-10">
                            <span class="badge bg-danger">Rechazada</span>
                        </div>
                        <img src="' . '../../' . htmlspecialchars($imagen['ruta']) . '" 
                             class="card-img-top img-fluid" 
                             alt="' . htmlspecialchars($imagen['titulo']) . '"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">' . htmlspecialchars($imagen['titulo']) . '</h5>
                            <p class="card-text text-truncate">' . htmlspecialchars($imagen['descripcion']) . '</p>
                            ' . (!empty($imagen['motivo_rechazo']) ? '<p class="text-danger"><small><strong>Motivo:</strong> ' . htmlspecialchars($imagen['motivo_rechazo']) . '</small></p>' : '') . '
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">Subida el ' . date("d/m/Y", strtotime($imagen['fecha_subida'])) . '</small>
                        </div>
                    </div>
                </div>';
            }
        }
        ?>
    </div>
</section>

    <!-- Footer -->
    <?php include '../../php/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>