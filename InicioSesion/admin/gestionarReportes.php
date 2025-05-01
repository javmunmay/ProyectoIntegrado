<?php
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_admin']) || $_SESSION['user_admin'] != 1) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

// Determinar filtro
$filtro = $_GET['filter'] ?? 'open';
$where = '';
switch ($filtro) {
    case 'open': $where = "WHERE estado != 'resuelta'"; break;
    case 'closed': $where = "WHERE estado = 'resuelta'"; break;
    case 'high': $where = "WHERE prioridad = 'alta' AND estado != 'resuelta'"; break;
    default: $where = "WHERE estado != 'resuelta'"; break;
}

// Obtener reportes (incidencias)
$reportes = [];
$query = "SELECT id, nombre_contacto, correo_contacto, titulo, descripcion, prioridad, estado, fecha_creacion 
          FROM incidencias 
          $where 
          ORDER BY 
            CASE WHEN estado = 'pendiente' THEN 0 
                 WHEN estado = 'en_proceso' THEN 1 
                 ELSE 2 END,
            CASE WHEN prioridad = 'alta' THEN 0 
                 WHEN prioridad = 'media' THEN 1 
                 ELSE 2 END,
            fecha_creacion DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reportes[] = $row;
    }
}

// Obtener detalles de un reporte específico para el modal
$reporte_detalle = null;
if (isset($_GET['ver_reporte'])) {
    $id = intval($_GET['ver_reporte']);
    $query = "SELECT * FROM incidencias WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reporte_detalle = $result->fetch_assoc();
}

// Procesar acciones (resolver, reabrir)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['resolver_reporte'])) {
        $id = intval($_POST['id']);
        $query = "UPDATE incidencias SET estado = 'resuelta' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: gestionarReportes.php?success=1");
        exit();
    } elseif (isset($_POST['reabrir_reporte'])) {
        $id = intval($_POST['id']);
        $query = "UPDATE incidencias SET estado = 'pendiente' WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: gestionarReportes.php?success=2");
        exit();
    } elseif (isset($_POST['cambiar_estado'])) {
        $id = intval($_POST['id']);
        $estado = $conn->real_escape_string($_POST['estado']);
        $query = "UPDATE incidencias SET estado = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
        header("Location: gestionarReportes.php?success=3");
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
    <title>Gestionar Reportes - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/stylesIndex.css">
    <style>
        .badge-prioridad-alta { background-color: #dc3545; }
        .badge-prioridad-media { background-color: #ffc107; color: #000; }
        .badge-prioridad-baja { background-color: #17a2b8; }
        
        .badge-estado-pendiente { background-color: #0d6efd; }
        .badge-estado-en_proceso { background-color: #fd7e14; }
        .badge-estado-resuelta { background-color: #198754; }
        
        .action-buttons .btn {
            margin-right: 5px;
        }
        .action-buttons .btn:last-child {
            margin-right: 0;
        }
        
        .modal-dialog {
            max-width: 700px;
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
        <h1 class="mb-4">Gestión de Reportes</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                switch($_GET['success']) {
                    case 1: echo 'Reporte marcado como resuelto'; break;
                    case 2: echo 'Reporte reabierto'; break;
                    case 3: echo 'Estado del reporte actualizado'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Reportes</h5>
                    <div class="btn-group">
                        <a href="?filter=open" class="btn btn-outline-primary <?php echo $filtro == 'open' ? 'active' : ''; ?>">Abiertos</a>
                        <a href="?filter=closed" class="btn btn-outline-success <?php echo $filtro == 'closed' ? 'active' : ''; ?>">Resueltos</a>
                        <a href="?filter=high" class="btn btn-outline-danger <?php echo $filtro == 'high' ? 'active' : ''; ?>">Urgentes</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Contacto</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportes as $reporte): ?>
                            <tr>
                                <td><?php echo $reporte['id']; ?></td>
                                <td><?php echo htmlspecialchars($reporte['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($reporte['nombre_contacto']); ?></td>
                                <td>
                                    <span class="badge badge-prioridad-<?php echo $reporte['prioridad'] == 'alta' ? 'alta' : 
                                              ($reporte['prioridad'] == 'media' ? 'media' : 'baja'); ?>">
                                        <?php echo ucfirst($reporte['prioridad']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-estado-<?php echo str_replace(' ', '_', $reporte['estado']); ?>">
                                        <?php echo str_replace('_', ' ', ucfirst($reporte['estado'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($reporte['fecha_creacion'])); ?></td>
                                <td class="action-buttons">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detalleReporteModal" 
                                            onclick="cargarDetalleReporte(<?php echo $reporte['id']; ?>)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if ($reporte['estado'] != 'resuelta'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $reporte['id']; ?>">
                                        <button type="submit" name="resolver_reporte" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $reporte['id']; ?>">
                                        <button type="submit" name="reabrir_reporte" class="btn btn-sm btn-warning">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles del reporte -->
    <div class="modal fade" id="detalleReporteModal" tabindex="-1" aria-labelledby="detalleReporteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleReporteModalLabel">Detalles del Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($reporte_detalle): ?>
                    <div class="mb-4">
                        <h4><?php echo htmlspecialchars($reporte_detalle['titulo']); ?></h4>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="badge badge-prioridad-<?php echo $reporte_detalle['prioridad'] == 'alta' ? 'alta' : 
                                      ($reporte_detalle['prioridad'] == 'media' ? 'media' : 'baja'); ?>">
                                <?php echo ucfirst($reporte_detalle['prioridad']); ?>
                            </span>
                            <span class="badge badge-estado-<?php echo str_replace(' ', '_', $reporte_detalle['estado']); ?>">
                                <?php echo str_replace('_', ' ', ucfirst($reporte_detalle['estado'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Información del Contacto</h6>
                        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($reporte_detalle['nombre_contacto']); ?></p>
                        <p><strong>Correo:</strong> <?php echo htmlspecialchars($reporte_detalle['correo_contacto']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Descripción</h6>
                        <p><?php echo nl2br(htmlspecialchars($reporte_detalle['descripcion'])); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Fecha de Creación</h6>
                        <p><?php echo date('d/m/Y H:i', strtotime($reporte_detalle['fecha_creacion'])); ?></p>
                    </div>
                    
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="id" value="<?php echo $reporte_detalle['id']; ?>">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Cambiar Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="pendiente" <?php echo $reporte_detalle['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="en_proceso" <?php echo $reporte_detalle['estado'] == 'en_proceso' ? 'selected' : ''; ?>>En Proceso</option>
                                <option value="resuelta" <?php echo $reporte_detalle['estado'] == 'resuelta' ? 'selected' : ''; ?>>Resuelta</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" name="cambiar_estado" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../php/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarDetalleReporte(id) {
            // Actualizar la URL con el parámetro ver_reporte
            const url = new URL(window.location.href);
            url.searchParams.set('ver_reporte', id);
            window.history.pushState({}, '', url);
            
            // Recargar solo el modal con los nuevos datos
            const modal = document.getElementById('detalleReporteModal');
            const modalBody = modal.querySelector('.modal-body');
            
            // Mostrar spinner mientras se carga
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            `;
            
            // Cargar los datos del reporte
            fetch(`gestionarReportes.php?ver_reporte=${id}`)
                .then(response => response.text())
                .then(html => {
                    // Extraer solo el contenido del modal del HTML recibido
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newModalBody = doc.querySelector('#detalleReporteModal .modal-body');
                    
                    // Reemplazar el contenido del modal
                    modalBody.innerHTML = newModalBody.innerHTML;
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            Error al cargar los detalles del reporte. Por favor, intente nuevamente.
                        </div>
                    `;
                });
        }
        
        // Limpiar el parámetro ver_reporte al cerrar el modal
        document.getElementById('detalleReporteModal').addEventListener('hidden.bs.modal', function () {
            const url = new URL(window.location.href);
            url.searchParams.delete('ver_reporte');
            window.history.pushState({}, '', url);
        });
    </script>
</body>
</html>