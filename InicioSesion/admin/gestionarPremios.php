<?php
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id'])) {
    header("Location: https://41183897.servicio-online.net/InicioSesion/inicioSesion.php");
    exit();
}

// Obtener el ID de bases_concurso válido
$sql_bases = "SELECT id FROM bases_concurso ORDER BY id DESC LIMIT 1";
$result_bases = $conn->query($sql_bases);
$bases_data = $result_bases->fetch_assoc();
$bases_concurso_id = $bases_data ? $bases_data['id'] : null;

if (!$bases_concurso_id) {
    die("No se encontró una configuración de bases del concurso");
}

// Obtener los premios actuales del concurso
$sql = "SELECT * FROM premios_concurso WHERE bases_concurso_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bases_concurso_id);
$stmt->execute();
$result = $stmt->get_result();
$premios_actuales = $result->fetch_assoc();

// Si no hay premios, inicializar con valores por defecto
if (!$premios_actuales) {
    $premios_actuales = [
        'primero' => '',
        'segundo' => '',
        'tercero' => '',
        'importe_primero' => 0.00,
        'importe_segundo' => 0.00,
        'importe_tercero' => 0.00
    ];
}

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y sanitizar los datos
    $primero = trim($_POST['primero']);
    $segundo = trim($_POST['segundo']);
    $tercero = trim($_POST['tercero']);
    $importe_primero = number_format(floatval($_POST['importe_primero']), 2, '.', '');
    $importe_segundo = number_format(floatval($_POST['importe_segundo']), 2, '.', '');
    $importe_tercero = number_format(floatval($_POST['importe_tercero']), 2, '.', '');

    // Validar que al menos haya un premio definido
    if (empty($primero) && empty($segundo) && empty($tercero)) {
        $error = "Debe definir al menos un premio";
    } else {
        // Verificar si ya existe un registro
        $check_sql = "SELECT id FROM premios_concurso WHERE bases_concurso_id = ? LIMIT 1";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $bases_concurso_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            // Actualizar el registro existente
            $sql = "UPDATE premios_concurso SET 
                    primero = ?, 
                    segundo = ?, 
                    tercero = ?, 
                    importe_primero = ?, 
                    importe_segundo = ?, 
                    importe_tercero = ? 
                    WHERE bases_concurso_id = ?";
        } else {
            // Insertar nuevo registro
            $sql = "INSERT INTO premios_concurso 
                    (primero, segundo, tercero, 
                    importe_primero, importe_segundo, importe_tercero, 
                    bases_concurso_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error al preparar la consulta: " . $conn->error);
        }

        if ($check_result->num_rows > 0) {
            $stmt->bind_param(
                "ssssssi",
                $primero,
                $segundo,
                $tercero,
                $importe_primero,
                $importe_segundo,
                $importe_tercero,
                $bases_concurso_id
            );
        } else {
            $stmt->bind_param(
                "ssssssi",
                $primero,
                $segundo,
                $tercero,
                $importe_primero,
                $importe_segundo,
                $importe_tercero,
                $bases_concurso_id
            );
        }

        if ($stmt->execute()) {
            $mensaje_exito = "Los premios del concurso se han actualizado correctamente";
            // Actualizar los premios actuales para mostrarlos
            $premios_actuales = [
                'primero' => $primero,
                'segundo' => $segundo,
                'tercero' => $tercero,
                'importe_primero' => $importe_primero,
                'importe_segundo' => $importe_segundo,
                'importe_tercero' => $importe_tercero
            ];
        } else {
            $error = "Error al actualizar los premios del concurso: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Premios del Concurso - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
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

        .premio-card {
            border-left: 4px solid;
            margin-bottom: 20px;
        }

        .premio-1 {
            border-left-color: #FFD700; /* Oro */
            background-color: rgba(255, 215, 0, 0.05);
        }

        .premio-2 {
            border-left-color: #C0C0C0; /* Plata */
            background-color: rgba(192, 192, 192, 0.05);
        }

        .premio-3 {
            border-left-color: #CD7F32; /* Bronce */
            background-color: rgba(205, 127, 50, 0.05);
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container.mt-5 {
            flex: 1;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            margin-top: auto;
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
                <i class="bi bi-trophy me-2"></i>Gestionar Premios del Concurso
            </h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($mensaje_exito)): ?>
                <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
            <?php endif; ?>

            <form method="POST" action="gestionarPremios.php">
                <!-- Premio 1er lugar -->
                <div class="card premio-card premio-1 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            <i class="bi bi-trophy-fill"></i> Primer Premio
                        </h5>
                        <div class="mb-3">
                            <label for="primero" class="form-label">Descripción del premio</label>
                            <input type="text" class="form-control" id="primero" name="primero"
                                value="<?php echo htmlspecialchars($premios_actuales['primero']); ?>"
                                placeholder="Ej: Cámara profesional, curso de fotografía, etc.">
                        </div>
                        <div class="mb-3">
                            <label for="importe_primero" class="form-label">Importe (€)</label>
                            <input type="number" class="form-control" id="importe_primero" name="importe_primero"
                                step="0.01" min="0" 
                                value="<?php echo number_format($premios_actuales['importe_primero'], 2); ?>">
                        </div>
                    </div>
                </div>

                <!-- Premio 2do lugar -->
                <div class="card premio-card premio-2 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-secondary">
                            <i class="bi bi-trophy-fill"></i> Segundo Premio
                        </h5>
                        <div class="mb-3">
                            <label for="segundo" class="form-label">Descripción del premio</label>
                            <input type="text" class="form-control" id="segundo" name="segundo"
                                value="<?php echo htmlspecialchars($premios_actuales['segundo']); ?>"
                                placeholder="Ej: Objetivo para cámara, membresía premium, etc.">
                        </div>
                        <div class="mb-3">
                            <label for="importe_segundo" class="form-label">Importe (€)</label>
                            <input type="number" class="form-control" id="importe_segundo" name="importe_segundo"
                                step="0.01" min="0" 
                                value="<?php echo number_format($premios_actuales['importe_segundo'], 2); ?>">
                        </div>
                    </div>
                </div>

                <!-- Premio 3er lugar -->
                <div class="card premio-card premio-3 mb-4">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #CD7F32;">
                            <i class="bi bi-trophy-fill"></i> Tercer Premio
                        </h5>
                        <div class="mb-3">
                            <label for="tercero" class="form-label">Descripción del premio</label>
                            <input type="text" class="form-control" id="tercero" name="tercero"
                                value="<?php echo htmlspecialchars($premios_actuales['tercero']); ?>"
                                placeholder="Ej: Libro de fotografía, accesorios, etc.">
                        </div>
                        <div class="mb-3">
                            <label for="importe_tercero" class="form-label">Importe (€)</label>
                            <input type="number" class="form-control" id="importe_tercero" name="importe_tercero"
                                step="0.01" min="0" 
                                value="<?php echo number_format($premios_actuales['importe_tercero'], 2); ?>">
                        </div>
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

    <br><br>

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación básica del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const primero = document.getElementById('primero').value.trim();
            const segundo = document.getElementById('segundo').value.trim();
            const tercero = document.getElementById('tercero').value.trim();
            
            if (primero === '' && segundo === '' && tercero === '') {
                e.preventDefault();
                alert('Debe definir al menos un premio');
            }
        });
    </script>
</body>

</html>