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

// Función para obtener imágenes del usuario según su estado
function obtenerImagenes($conn, $usuario_id, $estado) {
    $query = "SELECT * FROM imagenes WHERE usuario_id = ? AND estado = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $usuario_id, $estado);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Función para buscar imágenes
function buscarImagenes($conn, $usuario_id, $busqueda) {
    $query = "SELECT * FROM imagenes WHERE usuario_id = ? AND (titulo LIKE ? OR descripcion LIKE ?)";
    $stmt = $conn->prepare($query);
    $busquedaParam = "%$busqueda%";
    $stmt->bind_param("iss", $usuario_id, $busquedaParam, $busquedaParam);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['subir_imagen'])) {
        // Procesar subida de imagen
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);
        $fecha = date('Y-m-d H:i:s');

        // Subir archivo
        $ruta = '../../fotosDeUsuarios/' . basename($_FILES['imagen']['name']);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], '../../' . $ruta)) {
            $query = "INSERT INTO imagenes (usuario_id, titulo, descripcion, ruta, fecha_subida, estado) 
                      VALUES (?, ?, ?, ?, ?, 'pendiente')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issss", $usuario_id, $titulo, $descripcion, $ruta, $fecha);
            $stmt->execute();
            header("Location: misImagenes.php?success=1");
            exit();
        } else {
            header("Location: misImagenes.php?error=1");
            exit();
        }
    } elseif (isset($_POST['editar_imagen'])) {
        // Procesar edición de imagen
        $id = intval($_POST['id']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);

        $query = "UPDATE imagenes SET titulo = ?, descripcion = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $titulo, $descripcion, $id, $usuario_id);
        $stmt->execute();
        header("Location: misImagenes.php?success=2");
        exit();
    } elseif (isset($_POST['eliminar_imagen'])) {
        // Procesar eliminación de imagen
        $id = intval($_POST['id']);

        // Primero obtener la ruta para eliminar el archivo
        $query = "SELECT ruta FROM imagenes WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $imagen = $result->fetch_assoc();

        if ($imagen) {
            // Eliminar el archivo
            if (file_exists('../../' . $imagen['ruta'])) {
                unlink('../../' . $imagen['ruta']);
            }

            // Eliminar de la base de datos
            $query = "DELETE FROM imagenes WHERE id = ? AND usuario_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $id, $usuario_id);
            $stmt->execute();
        }

        header("Location: misImagenes.php?success=3");
        exit();
    }
}

// Obtener imágenes para mostrar
$imagenesActivas = obtenerImagenes($conn, $usuario_id, 'activo');
$imagenesPendientes = obtenerImagenes($conn, $usuario_id, 'pendiente');
$imagenesRechazadas = obtenerImagenes($conn, $usuario_id, 'inactivo');

$conn->close();
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
    <style>
        .card-img-top {
            height: 250px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .card-img-top:hover {
            transform: scale(1.03);
        }

        .modal-img {
            max-height: 70vh;
            object-fit: contain;
        }

        .badge-estado {
            font-size: 0.8rem;
        }

        .badge-pendiente {
            background-color: #ffc107;
            color: #000;
        }

        .badge-activo {
            background-color: #198754;
        }

        .badge-inactivo {
            background-color: #dc3545;
        }

        #contenedor-imagenes {
            min-height: 300px;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            background-color: #f8f9fa;
            border-radius: 0.5rem;
        }

        .search-box {
            position: relative;
        }

        .search-box .btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
        }

        .nubeLike {
            background-color: #090643;
        }

        .btnEditar {
            background-color: white;
            border: solid 2px #090643;
            border-radius: 10px;
            transform: scale(1.10);
            margin-right: 7px;
        }

        .btnEliminar {
            background-color: white;
            border: solid 2px #EF0000;
            border-radius: 10px;
            transform: scale(1.10);
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

        .btn-outline-danger {
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .categoria-titulo {
            color: #090643;
        }
    </style>
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
                        <a class="nav-link" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="miPerfil.php">Mi Perfil</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="misImagenes.php">Mis Imágenes</a>
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
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sección Gestionar Mis Imágenes -->
    <div class="container mt-5">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php
                switch ($_GET['success']) {
                    case 1:
                        echo 'Imagen subida correctamente y en revisión';
                        break;
                    case 2:
                        echo 'Imagen actualizada correctamente';
                        break;
                    case 3:
                        echo 'Imagen eliminada correctamente';
                        break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php
                switch ($_GET['error']) {
                    case 1:
                        echo 'Error al subir la imagen';
                        break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="categoria-titulo mb-0">Mis Imágenes</h2>

            <a href="../../php/subir_imagen.php" class="btnSubir  ms-auto me-3">
                <i class="bi bi-cloud-arrow-up"></i> Subir
            </a>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Buscar imágenes..." id="buscarImagen">
                    <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <!--
            <div class="col-md-6">
                <select class="form-select" id="filtroOrden">
                    <option value="recientes">Más recientes primero</option>
                    <option value="antiguas">Más antiguas primero</option>
                    <option value="titulo-asc">Por título (A-Z)</option>
                    <option value="titulo-desc">Por título (Z-A)</option>
                </select>
            </div>
            -->
        </div>

        <!-- Contenedor de imágenes -->
        <div class="row g-4" id="contenedor-imagenes">
            <?php if (empty($imagenesActivas)): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-images fs-1 text-muted"></i>
                        <h4 class="mt-3">No tienes imágenes subidas</h4>
                        <p class="text-muted">Comienza compartiendo tus fotografías con la comunidad</p>
                        <a href="../../php/subir_imagen.php" class="btn btn-primary mt-2">
                            <i class="bi bi-cloud-arrow-up"></i> Subir mi primera imagen
                        </a>
                    </div>
                </div>
                <?php else:
                foreach ($imagenesActivas as $imagen):
                    $rutaImagen = '../../' . htmlspecialchars($imagen['ruta']);
                    $fechaFormateada = date('d/m/Y', strtotime($imagen['fecha_subida']));
                ?>
                    <div class="col-lg-4 col-md-6 imagen-card"
                        data-id="<?= $imagen['id'] ?>"
                        data-titulo="<?= htmlspecialchars(strtolower($imagen['titulo'])) ?>"
                        data-fecha="<?= strtotime($imagen['fecha_subida']) ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="position-relative">
                                <img src="<?= $rutaImagen ?>"
                                    class="card-img-top"
                                    alt="<?= htmlspecialchars($imagen['titulo']) ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#verImagenModal"
                                    onclick="cargarImagenModal('<?= $rutaImagen ?>', '<?= htmlspecialchars($imagen['titulo']) ?>')">
                                <span class="nubeLike position-absolute top-0 end-0 text-white px-2 py-1 m-2 rounded-pill small">
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
                                        <button class="btnEditar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editarImagenModal"
                                            onclick="cargarEditarModal(<?= $imagen['id'] ?>, '<?= htmlspecialchars($imagen['titulo']) ?>', '<?= htmlspecialchars($imagen['descripcion']) ?>')">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btnEliminar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#eliminarImagenModal"
                                            onclick="cargarEliminarModal(<?= $imagen['id'] ?>, '<?= htmlspecialchars($imagen['titulo']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>

    <!-- Sección Imágenes en Revisión -->
    <section id="revision-imagenes" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Imágenes en revisión</h2>
        <div class="row">
            <?php if (empty($imagenesPendientes)): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-images fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay imágenes en revisión</p>
                    </div>
                </div>
                <?php else:
                foreach ($imagenesPendientes as $imagen):
                    $rutaImagen = '../../' . htmlspecialchars($imagen['ruta']);
                ?>
                    <div class="col-md-4 mb-4 imagen-card"
                         data-id="<?= $imagen['id'] ?>"
                         data-titulo="<?= htmlspecialchars(strtolower($imagen['titulo'])) ?>"
                         data-fecha="<?= strtotime($imagen['fecha_subida']) ?>">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= $rutaImagen ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($imagen['titulo']) ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#verImagenModal"
                                onclick="cargarImagenModal('<?= $rutaImagen ?>', '<?= htmlspecialchars($imagen['titulo']) ?>')">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($imagen['titulo']) ?></h5>
                                <p class="card-text text-truncate"><?= htmlspecialchars($imagen['descripcion']) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?= date("d/m/Y", strtotime($imagen['fecha_subida'])) ?></small>
                                    <span class="badge badge-estado badge-pendiente">En revisión</span>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </section>

    <!-- Sección Imágenes No Aceptadas -->
    <section id="no-aceptadas-imagenes" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Imágenes no aceptadas</h2>
        <div class="row">
            <?php if (empty($imagenesRechazadas)): ?>
                <div class="col-12">
                    <div class="empty-state">
                        <i class="bi bi-x-circle fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay imágenes rechazadas</p>
                    </div>
                </div>
                <?php else:
                foreach ($imagenesRechazadas as $imagen):
                    $rutaImagen = '../../' . htmlspecialchars($imagen['ruta']);
                ?>
                    <div class="col-md-4 mb-4 imagen-card"
                         data-id="<?= $imagen['id'] ?>"
                         data-titulo="<?= htmlspecialchars(strtolower($imagen['titulo'])) ?>"
                         data-fecha="<?= strtotime($imagen['fecha_subida']) ?>">
                        <div class="card h-100 shadow-sm border-danger">
                            <div class="card-header bg-danger bg-opacity-10">
                                <span class="badge badge-estado badge-inactivo">Rechazada</span>
                            </div>
                            <img src="<?= $rutaImagen ?>"
                                class="card-img-top"
                                alt="<?= htmlspecialchars($imagen['titulo']) ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#verImagenModal"
                                onclick="cargarImagenModal('<?= $rutaImagen ?>', '<?= htmlspecialchars($imagen['titulo']) ?>')">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($imagen['titulo']) ?></h5>
                                <p class="card-text text-truncate"><?= htmlspecialchars($imagen['descripcion']) ?></p>
                                <?php if (!empty($imagen['motivo_rechazo'])): ?>
                                    <p class="text-danger"><small><strong>Motivo:</strong> <?= htmlspecialchars($imagen['motivo_rechazo']) ?></small></p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">Subida el <?= date("d/m/Y", strtotime($imagen['fecha_subida'])) ?></small>
                            </div>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../../php/footer.php'; ?>

    <!-- Modal Ver Imagen -->
    <div class="modal fade" id="verImagenModal" tabindex="-1" aria-labelledby="verImagenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verImagenModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" class="modal-img img-fluid" id="imagenModal">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Imagen -->
    <div class="modal fade" id="editarImagenModal" tabindex="-1" aria-labelledby="editarImagenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="misImagenes.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarImagenModalLabel">Editar Imagen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="editar_imagen" value="1">
                        <input type="hidden" name="id" id="editarImagenId">

                        <div class="mb-3">
                            <label for="editarTitulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="editarTitulo" name="titulo" required>
                        </div>

                        <div class="mb-3">
                            <label for="editarDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editarDescripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Imagen -->
    <div class="modal fade" id="eliminarImagenModal" tabindex="-1" aria-labelledby="eliminarImagenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="misImagenes.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eliminarImagenModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="eliminar_imagen" value="1">
                        <input type="hidden" name="id" id="eliminarImagenId">
                        <p>¿Estás seguro de que deseas eliminar la imagen "<span id="eliminarImagenTitulo"></span>"?</p>
                        <p class="text-danger">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para cargar imagen en el modal de visualización
        function cargarImagenModal(ruta, titulo) {
            document.getElementById('imagenModal').src = ruta;
            document.getElementById('verImagenModalLabel').textContent = titulo;
        }

        // Función para cargar datos en el modal de edición
        function cargarEditarModal(id, titulo, descripcion) {
            document.getElementById('editarImagenId').value = id;
            document.getElementById('editarTitulo').value = titulo;
            document.getElementById('editarDescripcion').value = descripcion;
        }

        // Función para cargar datos en el modal de eliminación
        function cargarEliminarModal(id, titulo) {
            document.getElementById('eliminarImagenId').value = id;
            document.getElementById('eliminarImagenTitulo').textContent = titulo;
        }

        // Función para convertir fecha de texto a timestamp
        function parsearFecha(textoFecha) {
            // Formato dd/mm/yyyy
            const partes = textoFecha.split('/');
            if (partes.length === 3) {
                return new Date(partes[2], partes[1] - 1, partes[0]).getTime();
            }
            return 0;
        }

        // Funcionalidad de búsqueda mejorada
        function realizarBusqueda() {
            const busqueda = document.getElementById('buscarImagen').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.imagen-card');
            
            if (busqueda === '') {
                // Si no hay búsqueda, mostrar todas las imágenes
                cards.forEach(card => card.style.display = 'block');
                return;
            }

            cards.forEach(card => {
                const titulo = card.getAttribute('data-titulo');
                const descripcion = card.querySelector('.card-text').textContent.toLowerCase();
                
                if (titulo.includes(busqueda) || descripcion.includes(busqueda)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Evento de búsqueda al hacer clic en el botón
        document.getElementById('btnBuscar').addEventListener('click', realizarBusqueda);

        // Evento de búsqueda al presionar Enter en el input
        document.getElementById('buscarImagen').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                realizarBusqueda();
            }
        });

        // Filtro de orden mejorado
        document.getElementById('filtroOrden').addEventListener('change', function() {
            const orden = this.value;
            const secciones = [
                'contenedor-imagenes', 
                'revision-imagenes', 
                'no-aceptadas-imagenes'
            ];

            secciones.forEach(seccionId => {
                const container = document.getElementById(seccionId);
                if (!container) return;

                const row = container.querySelector('.row');
                if (!row) return;

                const cards = Array.from(row.querySelectorAll('.imagen-card'));

                cards.sort((a, b) => {
                    const tituloA = a.getAttribute('data-titulo') || 
                                  a.querySelector('.card-title').textContent.toLowerCase();
                    const tituloB = b.getAttribute('data-titulo') || 
                                  b.querySelector('.card-title').textContent.toLowerCase();

                    // Obtener fechas de los atributos data o del texto
                    let fechaA, fechaB;
                    
                    if (a.hasAttribute('data-fecha')) {
                        fechaA = parseInt(a.getAttribute('data-fecha'));
                        fechaB = parseInt(b.getAttribute('data-fecha'));
                    } else {
                        const fechaTextA = a.querySelector('small.text-muted').textContent;
                        const fechaTextB = b.querySelector('small.text-muted').textContent;
                        fechaA = parsearFecha(fechaTextA);
                        fechaB = parsearFecha(fechaTextB);
                    }

                    switch(orden) {
                        case 'recientes':
                            return fechaB - fechaA;
                        case 'antiguas':
                            return fechaA - fechaB;
                        case 'titulo-asc':
                            return tituloA.localeCompare(tituloB);
                        case 'titulo-desc':
                            return tituloB.localeCompare(tituloA);
                        default:
                            return 0;
                    }
                });

                // Reordenar las cards en el DOM
                cards.forEach(card => row.appendChild(card));
            });
        });
    </script>
</body>
</html>