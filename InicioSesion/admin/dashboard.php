<?php 
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_admin']) || $_SESSION['user_admin'] != 1) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

$nombreUsuario = $_SESSION['user_nombre']; // Corregido: usar user_nombre en lugar de user_id['nombre']

// Obtener estadísticas
$stats = [
    'usuarios' => 0,
    'imagenes' => 0,
    'incidencias' => 0,
    'reportes' => 0
];

// Consulta para contar usuarios
$query = "SELECT COUNT(*) as total FROM Usuarios";
$result = $conn->query($query);
if ($result) {
    $stats['usuarios'] = $result->fetch_assoc()['total'];
}

// Consulta para contar imágenes
$query = "SELECT COUNT(*) as total FROM imagenes WHERE estado = 'activo'";
$result = $conn->query($query);
if ($result) {
    $stats['imagenes'] = $result->fetch_assoc()['total'];
}

// Consulta para contar incidencias
$query = "SELECT COUNT(*) as total FROM incidencias WHERE estado = 'pendiente'";
$result = $conn->query($query);
if ($result) {
    $stats['incidencias'] = $result->fetch_assoc()['total'];
}

// Consulta para contar reportes (asumiendo que son incidencias no resueltas)
$query = "SELECT COUNT(*) as total FROM incidencias WHERE estado != 'resuelta'";
$result = $conn->query($query);
if ($result) {
    $stats['reportes'] = $result->fetch_assoc()['total'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
    <style>
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            border: none;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .quick-action-btn {
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover {
            transform: scale(1.05);
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

    <section class="container mt-5">
        <h1 class="text-center mb-4 categoria-titulo">Panel de Administrador</h1>
        <div class="row mb-4">
            <div class="col-md-6 mx-auto text-center">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill"></i> Bienvenido <?php echo htmlspecialchars($nombreUsuario); ?> al panel de administración de pixFly
                </div>
            </div>
        </div>
        
        <!-- Estadísticas -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card card bg-primary text-white p-4 text-center">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <h3 class="stat-number"><?php echo $stats['usuarios']; ?></h3>
                    <p>Usuarios registrados</p>
                    <a href="gestionarUsuarios.php" class="btn btn-light btn-sm quick-action-btn">Gestionar</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card card bg-success text-white p-4 text-center">
                    <i class="bi bi-image-fill stat-icon"></i>
                    <h3 class="stat-number"><?php echo $stats['imagenes']; ?></h3>
                    <p>Imágenes activas</p>
                    <a href="gestionarImagenes.php" class="btn btn-light btn-sm quick-action-btn">Gestionar</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card card bg-warning text-dark p-4 text-center">
                    <i class="bi bi-exclamation-triangle-fill stat-icon"></i>
                    <h3 class="stat-number"><?php echo $stats['incidencias']; ?></h3>
                    <p>Incidencias pendientes</p>
                    <a href="gestionarIncidencias.php" class="btn btn-dark btn-sm quick-action-btn">Revisar</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card card bg-danger text-white p-4 text-center">
                    <i class="bi bi-ticket-fill stat-icon"></i>
                    <h3 class="stat-number"><?php echo $stats['reportes']; ?></h3>
                    <p>Tickets abiertos</p>
                    <a href="gestionarReportes.php" class="btn btn-light btn-sm quick-action-btn">Atender</a>
                </div>
            </div>
        </div>
        
        <!-- Acciones rápidas -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4">Acciones rápidas</h2>
                <div class="d-flex flex-wrap gap-3">
                    <a href="gestionarUsuarios.php?action=create" class="btn btn-outline-primary quick-action-btn">
                        <i class="bi bi-person-plus"></i> Crear nuevo usuario
                    </a>
                    <a href="gestionarImagenes.php?filter=pending" class="btn btn-outline-secondary quick-action-btn">
                        <i class="bi bi-images"></i> Revisar imágenes pendientes
                    </a>
                    <a href="gestionarIncidencias.php?filter=high" class="btn btn-outline-danger quick-action-btn">
                        <i class="bi bi-exclamation-octagon"></i> Incidencias de alta prioridad
                    </a>
                    <a href="gestionarReportes.php?filter=open" class="btn btn-outline-warning quick-action-btn">
                        <i class="bi bi-ticket-detailed"></i> Reportes sin resolver
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Últimas actividades (ejemplo) -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Actividad reciente</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-person text-primary"></i> Nuevo usuario registrado hoy
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-image text-success"></i> 5 imágenes subidas en las últimas 24 horas
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-exclamation-triangle text-warning"></i> 2 incidencias reportadas esta semana
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle text-success"></i> 1 ticket resuelto ayer
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Actualizar estadísticas cada 60 segundos
        setInterval(() => {
            fetch('../../php/actualizarDatos.php')
                .then(response => response.json())
                .then(data => {
                    document.querySelectorAll('.stat-number')[0].textContent = data.usuarios;
                    document.querySelectorAll('.stat-number')[1].textContent = data.imagenes;
                    document.querySelectorAll('.stat-number')[2].textContent = data.incidencias;
                    document.querySelectorAll('.stat-number')[3].textContent = data.reportes;
                });
        }, 60000);
    </script>
</body>
</html>