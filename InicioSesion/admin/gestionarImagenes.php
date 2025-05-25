<?php
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_admin']) || $_SESSION['user_admin'] != 1) {
    header("Location: https://41183897.servicio-online.net/InicioSesion/inicioSesion.php");
    exit();
}

// Determinar filtro
$filtro = $_GET['filter'] ?? 'all';
$where = '';
switch ($filtro) {
    case 'pending': $where = "WHERE estado = 'pendiente'"; break;
    case 'active': $where = "WHERE estado = 'activo'"; break;
    case 'inactive': $where = "WHERE estado = 'inactivo'"; break;
    default: $where = ''; break;
}

// Obtener imágenes
$imagenes = [];
$query = "SELECT i.id, i.titulo, i.ruta, i.fecha_subida, i.estado, u.nombre as usuario, u.id as usuario_id 
          FROM imagenes i 
          LEFT JOIN Usuarios u ON i.usuario_id = u.id 
          $where 
          ORDER BY i.fecha_subida DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $imagenes[] = $row;
    }
}

// Obtener lista de usuarios para el select
$usuarios_lista = [];
$query_usuarios = "SELECT id, nombre FROM Usuarios ORDER BY nombre";
$result_usuarios = $conn->query($query_usuarios);
if ($result_usuarios) {
    while ($row = $result_usuarios->fetch_assoc()) {
        $usuarios_lista[] = $row;
    }
}

// Procesar acciones (cambiar estado, eliminar, editar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cambiar_estado'])) {
        $id = intval($_POST['id']);
        $estado = $_POST['estado'];
        $query = "UPDATE imagenes SET estado = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
        header("Location: gestionarImagenes.php?success=1");
        exit();
    } elseif (isset($_POST['eliminar_imagen'])) {
        $id = intval($_POST['id']);
        // Primero obtener la ruta para eliminar el archivo
        $query = "SELECT ruta FROM imagenes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $imagen = $result->fetch_assoc();
        
        if ($imagen && file_exists('../../'.$imagen['ruta'])) {
            unlink('../../'.$imagen['ruta']);
        }
        
        // Luego eliminar de la base de datos
        $query = "DELETE FROM imagenes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: gestionarImagenes.php?success=2");
        exit();
    } elseif (isset($_POST['editar_imagen'])) {
        $id = intval($_POST['id']);
        $titulo = $conn->real_escape_string($_POST['titulo']);
        $usuario_id = intval($_POST['usuario_id']);
        $fecha = $conn->real_escape_string($_POST['fecha']);
        
        $query = "UPDATE imagenes SET titulo = ?, usuario_id = ?, fecha_subida = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisi", $titulo, $usuario_id, $fecha, $id);
        $stmt->execute();
        header("Location: gestionarImagenes.php?success=3");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Imágenes - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
    <style>
        .modal-img-preview {
            max-width: 100%;
            max-height: 300px;
            display: block;
            margin: 0 auto 20px;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        .action-buttons .btn:last-child {
            margin-right: 0;
        }

        html, body {
        height: 100%;
        margin: 0;
    }
    
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh; /* viewport height */
    }
    
    .container.mt-5 {
        flex: 1; /* Esto hace que el contenido principal ocupe todo el espacio disponible */
    }
    
    .footer {
        background-color: #f8f9fa;
        padding: 1rem 0;
        margin-top: auto; /* Empuja el footer hacia abajo */
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarUsuarios.php">Gestionar usuarios</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarImagenes.php">Gestionar imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarIncidencias.php">Gestionar incidencias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarReportes.php">Gestionar tickets/reportes</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger" href="../../php/logout.php">
                            Cerrar Sesión <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Imágenes</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                switch($_GET['success']) {
                    case 1: echo 'Estado de imagen actualizado'; break;
                    case 2: echo 'Imagen eliminada correctamente'; break;
                    case 3: echo 'Imagen actualizada correctamente'; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Imágenes</h5>
                    <div class="btn-group">
                        <a href="?filter=all" class="btn btn-outline-secondary <?php echo $filtro == 'all' ? 'active' : ''; ?>">Todas</a>
                        <a href="?filter=pending" class="btn btn-outline-warning <?php echo $filtro == 'pending' ? 'active' : ''; ?>">Pendientes</a>
                        <a href="?filter=active" class="btn btn-outline-success <?php echo $filtro == 'active' ? 'active' : ''; ?>">Activas</a>
                        <a href="?filter=inactive" class="btn btn-outline-danger <?php echo $filtro == 'inactive' ? 'active' : ''; ?>">Inactivas</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Título</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($imagenes as $imagen): ?>
                            <tr>
                                <td><?php echo $imagen['id']; ?></td>
                                <td>
                                    <img src="../../<?php echo htmlspecialchars($imagen['ruta']); ?>" 
                                         alt="<?php echo htmlspecialchars($imagen['titulo']); ?>" 
                                         style="width: 100px; height: auto;">
                                </td>
                                <td><?php echo htmlspecialchars($imagen['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($imagen['usuario']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($imagen['fecha_subida'])); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $imagen['id']; ?>">
                                        <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pendiente" <?php echo $imagen['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="activo" <?php echo $imagen['estado'] == 'activo' ? 'selected' : ''; ?>>Activo</option>
                                            <option value="inactivo" <?php echo $imagen['estado'] == 'inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                        <input type="hidden" name="cambiar_estado" value="1">
                                    </form>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarImagenModal" 
                                            onclick="cargarDatosModal(<?php echo $imagen['id']; ?>, '<?php echo htmlspecialchars($imagen['titulo'], ENT_QUOTES); ?>', <?php echo $imagen['usuario_id']; ?>, '<?php echo date('Y-m-d', strtotime($imagen['fecha_subida'])); ?>', '../../<?php echo htmlspecialchars($imagen['ruta']); ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $imagen['id']; ?>">
                                        <button type="submit" name="eliminar_imagen" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta imagen?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar imagen -->
    <div class="modal fade" id="editarImagenModal" tabindex="-1" aria-labelledby="editarImagenModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="gestionarImagenes.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarImagenModalLabel">Editar Imagen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <img id="imagenPreview" src="" alt="Vista previa" class="modal-img-preview">
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" name="id" id="imagenId">
                                <input type="hidden" name="editar_imagen" value="1">
                                
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="usuario_id" class="form-label">Usuario</label>
                                    <select class="form-select" id="usuario_id" name="usuario_id" required>
                                        <?php foreach ($usuarios_lista as $usuario): ?>
                                            <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="fecha" class="form-label">Fecha de subida</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../php/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarDatosModal(id, titulo, usuarioId, fecha, rutaImagen) {
            document.getElementById('imagenId').value = id;
            document.getElementById('titulo').value = titulo;
            document.getElementById('usuario_id').value = usuarioId;
            document.getElementById('fecha').value = fecha;
            document.getElementById('imagenPreview').src = rutaImagen;
        }
    </script>
</body>
</html>