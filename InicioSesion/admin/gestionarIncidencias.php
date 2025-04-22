<?php
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_admin']) || $_SESSION['user_admin'] != 1) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

// Determinar filtro
$filtro = $_GET['filter'] ?? 'all';
$where = '';
switch ($filtro) {
    case 'high': $where = "WHERE prioridad = 'alta'"; break;
    case 'medium': $where = "WHERE prioridad = 'media'"; break;
    case 'low': $where = "WHERE prioridad = 'baja'"; break;
    case 'pending': $where = "WHERE estado = 'pendiente'"; break;
    case 'process': $where = "WHERE estado = 'en_proceso'"; break;
    case 'resolved': $where = "WHERE estado = 'resuelta'"; break;
    default: $where = ''; break;
}

// Obtener incidencias
$incidencias = [];
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
        $incidencias[] = $row;
    }
}

// Procesar acciones (cambiar estado, prioridad)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cambiar_estado'])) {
        $id = intval($_POST['id']);
        $estado = $_POST['estado'];
        $query = "UPDATE incidencias SET estado = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $estado, $id);
        $stmt->execute();
        header("Location: gestionarIncidencias.php?success=1");
        exit();
    } elseif (isset($_POST['cambiar_prioridad'])) {
        $id = intval($_POST['id']);
        $prioridad = $_POST['prioridad'];
        $query = "UPDATE incidencias SET prioridad = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $prioridad, $id);
        $stmt->execute();
        header("Location: gestionarIncidencias.php?success=2");
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
    <title>Gestionar Incidencias - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/stylesIndex.css">
    <style>
        .badge-prioridad-alta {
            background-color: #dc3545;
        }
        .badge-prioridad-media {
            background-color: #ffc107;
            color: #000;
        }
        .badge-prioridad-baja {
            background-color: #0dcaf0;
            color: #000;
        }
        .badge-estado-pendiente {
            background-color: #0d6efd;
        }
        .badge-estado-en_proceso {
            background-color: #fd7e14;
        }
        .badge-estado-resuelta {
            background-color: #198754;
        }
        .badge-estado-cancelada {
            background-color: #6c757d;
        }
        .descripcion-incidencia {
            white-space: pre-wrap;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
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
                        <a class="btn btn-outline-danger" href="../cerrar_sesion.php">
                            Cerrar Sesión <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Incidencias</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_GET['success'] == 1 ? 'Estado actualizado' : 'Prioridad actualizada'; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lista de Incidencias</h5>
                    <div class="btn-group">
                        <a href="?filter=all" class="btn btn-outline-secondary <?php echo $filtro == 'all' ? 'active' : ''; ?>">Todas</a>
                        <a href="?filter=high" class="btn btn-outline-danger <?php echo $filtro == 'high' ? 'active' : ''; ?>">Alta</a>
                        <a href="?filter=medium" class="btn btn-outline-warning <?php echo $filtro == 'medium' ? 'active' : ''; ?>">Media</a>
                        <a href="?filter=low" class="btn btn-outline-info <?php echo $filtro == 'low' ? 'active' : ''; ?>">Baja</a>
                        <a href="?filter=pending" class="btn btn-outline-primary <?php echo $filtro == 'pending' ? 'active' : ''; ?>">Pendientes</a>
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
                            <?php foreach ($incidencias as $incidencia): ?>
                            <tr>
                                <td><?php echo $incidencia['id']; ?></td>
                                <td><?php echo htmlspecialchars($incidencia['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($incidencia['nombre_contacto']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $incidencia['prioridad'] == 'alta' ? 'badge-prioridad-alta' : 
                                             ($incidencia['prioridad'] == 'media' ? 'badge-prioridad-media' : 'badge-prioridad-baja');
                                    ?>">
                                        <?php echo ucfirst($incidencia['prioridad']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php 
                                        echo $incidencia['estado'] == 'pendiente' ? 'badge-estado-pendiente' : 
                                             ($incidencia['estado'] == 'en_proceso' ? 'badge-estado-en_proceso' : 
                                             ($incidencia['estado'] == 'resuelta' ? 'badge-estado-resuelta' : 'badge-estado-cancelada'));
                                    ?>">
                                        <?php echo str_replace('_', ' ', ucfirst($incidencia['estado'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($incidencia['fecha_creacion'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detalleIncidenciaModal" 
                                            onclick="cargarDatosModal(
                                                <?php echo $incidencia['id']; ?>, 
                                                '<?php echo htmlspecialchars($incidencia['titulo'], ENT_QUOTES); ?>', 
                                                '<?php echo htmlspecialchars($incidencia['nombre_contacto'], ENT_QUOTES); ?>', 
                                                '<?php echo htmlspecialchars($incidencia['correo_contacto'], ENT_QUOTES); ?>', 
                                                '<?php echo $incidencia['prioridad']; ?>', 
                                                '<?php echo $incidencia['estado']; ?>', 
                                                '<?php echo date('d/m/Y H:i', strtotime($incidencia['fecha_creacion'])); ?>', 
                                                `<?php echo htmlspecialchars($incidencia['descripcion'], ENT_QUOTES); ?>`
                                            )">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles de incidencia -->
    <div class="modal fade" id="detalleIncidenciaModal" tabindex="-1" aria-labelledby="detalleIncidenciaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalleIncidenciaModalLabel">Detalles de Incidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="modalId"></span></p>
                            <p><strong>Título:</strong> <span id="modalTitulo"></span></p>
                            <p><strong>Contacto:</strong> <span id="modalContacto"></span></p>
                            <p><strong>Correo:</strong> <span id="modalCorreo"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Prioridad:</strong> <span id="modalPrioridad" class="badge"></span></p>
                            <p><strong>Estado:</strong> <span id="modalEstado" class="badge"></span></p>
                            <p><strong>Fecha creación:</strong> <span id="modalFecha"></span></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Descripción:</h6>
                        <div class="descripcion-incidencia" id="modalDescripcion"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../php/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarDatosModal(id, titulo, contacto, correo, prioridad, estado, fecha, descripcion) {
            document.getElementById('modalId').textContent = id;
            document.getElementById('modalTitulo').textContent = titulo;
            document.getElementById('modalContacto').textContent = contacto;
            document.getElementById('modalCorreo').textContent = correo;
            document.getElementById('modalFecha').textContent = fecha;
            document.getElementById('modalDescripcion').textContent = descripcion;
            
            // Configurar prioridad
            const badgePrioridad = document.getElementById('modalPrioridad');
            badgePrioridad.textContent = prioridad.charAt(0).toUpperCase() + prioridad.slice(1);
            badgePrioridad.className = 'badge ' + (
                prioridad === 'alta' ? 'badge-prioridad-alta' : 
                (prioridad === 'media' ? 'badge-prioridad-media' : 'badge-prioridad-baja')
            );
            
            // Configurar estado
            const badgeEstado = document.getElementById('modalEstado');
            const estadoTexto = estado.replace('_', ' ');
            badgeEstado.textContent = estadoTexto.charAt(0).toUpperCase() + estadoTexto.slice(1);
            badgeEstado.className = 'badge ' + (
                estado === 'pendiente' ? 'badge-estado-pendiente' : 
                (estado === 'en_proceso' ? 'badge-estado-en_proceso' : 
                (estado === 'resuelta' ? 'badge-estado-resuelta' : 'badge-estado-cancelada'))
            );
        }
    </script>
</body>
</html>