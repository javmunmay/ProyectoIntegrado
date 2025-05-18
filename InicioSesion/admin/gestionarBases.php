<?php
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

// Obtener las bases actuales del concurso
$sql = "SELECT * FROM bases_concurso ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$bases_actuales = $result->fetch_assoc();

// Si no hay bases, inicializar con valores por defecto
if (!$bases_actuales) {
    $bases_actuales = [
        'fecha_inicio_concurso' => date('Y-m-d'),
        'fecha_fin_concurso' => date('Y-m-d', strtotime('+1 month')),
        'max_imagenes_por_usuario' => 5,
        'extensiones_permitidas' => 'jpg,jpeg,png',
        'tamano_maximo_mb' => 10,
        'votos_por_usuario' => 10
    ];
}

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $max_imagenes = intval($_POST['max_imagenes']);
    $extensiones = preg_replace('/\s+/', '', $_POST['extensiones']); // Eliminar espacios
    $tamano_maximo = intval($_POST['tamano_maximo']);
    $votos_permitidos = intval($_POST['votos_permitidos']);
    
    // Validar fechas
    if (strtotime($fecha_fin) < strtotime($fecha_inicio)) {
        $error = "La fecha de fin no puede ser anterior a la fecha de inicio";
    } else {
        // Insertar o actualizar las bases en la base de datos
        $sql = "INSERT INTO bases_concurso 
                (fecha_inicio_concurso, fecha_fin_concurso, max_imagenes_por_usuario, 
                 extensiones_permitidas, tamano_maximo_mb, votos_por_usuario, usuario_admin_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissii", 
            $fecha_inicio, $fecha_fin, $max_imagenes,
            $extensiones, $tamano_maximo, $votos_permitidos, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $mensaje_exito = "Las bases del concurso se han actualizado correctamente";
            // Actualizar las bases actuales para mostrarlas
            $bases_actuales = [
                'fecha_inicio_concurso' => $fecha_inicio,
                'fecha_fin_concurso' => $fecha_fin,
                'max_imagenes_por_usuario' => $max_imagenes,
                'extensiones_permitidas' => $extensiones,
                'tamano_maximo_mb' => $tamano_maximo,
                'votos_por_usuario' => $votos_permitidos
            ];
        } else {
            $error = "Error al actualizar las bases del concurso: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Bases del Concurso - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-title {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .form-label {
            font-weight: 600;
        }
        .current-value {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 15px;
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

    <div class="container py-5">
        <div class="form-container">
            <h2 class="text-center form-title">
                <i class="bi bi-journal-text me-2"></i>Gestionar Bases del Concurso
            </h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($mensaje_exito)): ?>
                <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="gestionarBases.php">
                <!-- Fechas del concurso -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                               value="<?php echo $bases_actuales['fecha_inicio_concurso']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha de fin</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                               value="<?php echo $bases_actuales['fecha_fin_concurso']; ?>" required>
                    </div>
                </div>
                
                <!-- Configuración de imágenes -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="max_imagenes" class="form-label">Máximo de imágenes por usuario</label>
                        <input type="number" class="form-control" id="max_imagenes" name="max_imagenes" 
                               min="1" max="20" value="<?php echo $bases_actuales['max_imagenes_por_usuario']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="extensiones" class="form-label">Extensiones permitidas (separadas por comas)</label>
                        <input type="text" class="form-control" id="extensiones" name="extensiones" 
                               value="<?php echo $bases_actuales['extensiones_permitidas']; ?>" required>
                        <small class="text-muted">Ejemplo: jpg,png,gif</small>
                    </div>
                    <div class="col-md-4">
                        <label for="tamano_maximo" class="form-label">Tamaño máximo por imagen (MB)</label>
                        <input type="number" class="form-control" id="tamano_maximo" name="tamano_maximo" 
                               min="1" max="50" value="<?php echo $bases_actuales['tamano_maximo_mb']; ?>" required>
                    </div>
                </div>
                
                <!-- Sistema de votación -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="votos_permitidos" class="form-label">Votos permitidos por usuario</label>
                        <input type="number" class="form-control" id="votos_permitidos" name="votos_permitidos" 
                               min="1" max="100" value="<?php echo $bases_actuales['votos_por_usuario']; ?>" required>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="dashboard.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al panel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación básica del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const fechaInicio = new Date(document.getElementById('fecha_inicio').value);
            const fechaFin = new Date(document.getElementById('fecha_fin').value);
            
            if (fechaFin < fechaInicio) {
                e.preventDefault();
                alert('La fecha de fin no puede ser anterior a la fecha de inicio');
            }
        });
    </script>
</body>
</html>