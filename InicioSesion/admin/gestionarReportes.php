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
$query = "SELECT id, nombre_contacto, correo_contacto, titulo, prioridad, estado, fecha_creacion 
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
        <h1 class="mb-4">Gestión de Reportes</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_GET['success'] == 1 ? 'Reporte marcado como resuelto' : 'Reporte reabierto'; ?>
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
                                    <span class="badge 
                                        <?php echo $reporte['prioridad'] == 'alta' ? 'bg-danger' : 
                                              ($reporte['prioridad'] == 'media' ? 'bg-warning' : 'bg-info'); ?>">
                                        <?php echo ucfirst($reporte['prioridad']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        <?php echo $reporte['estado'] == 'pendiente' ? 'bg-primary' : 
                                              ($reporte['estado'] == 'en_proceso' ? 'bg-warning' : 'bg-success'); ?>">
                                        <?php echo str_replace('_', ' ', ucfirst($reporte['estado'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($reporte['fecha_creacion'])); ?></td>
                                <td>
                                    <a href="detalle_reporte.php?id=<?php echo $reporte['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
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

    <?php include '../../php/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>