<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];
$mensaje_error = '';
$mensaje_exito = '';
$puede_subir = true;

// Obtener las bases del concurso activo
$sql_bases = "SELECT * FROM bases_concurso 
              WHERE fecha_inicio_concurso <= CURDATE() 
              AND fecha_fin_concurso >= CURDATE() 
              ORDER BY id DESC LIMIT 1";
$result_bases = $conn->query($sql_bases);

if ($result_bases->num_rows > 0) {
    $bases = $result_bases->fetch_assoc();
    $max_imagenes = $bases['max_imagenes_por_usuario'];
    $extensiones_permitidas = explode(',', $bases['extensiones_permitidas']);
    $tamano_maximo = $bases['tamano_maximo_mb'] * 1024 * 1024; // Convertir MB a bytes
    
    // Verificar cuántas imágenes ha subido el usuario (sin importar el estado)
    $sql_imagenes_usuario = "SELECT COUNT(*) as total FROM imagenes 
                            WHERE usuario_id = ?";
    $stmt_imagenes = $conn->prepare($sql_imagenes_usuario);
    $stmt_imagenes->bind_param("i", $usuario_id);
    $stmt_imagenes->execute();
    $result_imagenes = $stmt_imagenes->get_result();
    $imagenes_subidas = $result_imagenes->fetch_assoc()['total'];
    $stmt_imagenes->close();
    
    $imagenes_restantes = $max_imagenes - $imagenes_subidas;
    
    if ($imagenes_restantes <= 0) {
        $puede_subir = false;
        $mensaje_error = "Has alcanzado el límite máximo de $max_imagenes imágenes para este concurso.";
    }
} else {
    // Si no hay concurso activo, permitir subida sin límites
    $max_imagenes = 0;
    $imagenes_restantes = 1; // Para que siempre muestre que puede subir
    $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
    $tamano_maximo = 10 * 1024 * 1024; // 10MB por defecto
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $puede_subir) {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['imagen']['name'];
        $tipo_archivo = $_FILES['imagen']['type'];
        $tamano_archivo = $_FILES['imagen']['size'];
        $temp_archivo = $_FILES['imagen']['tmp_name'];
        $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

        // Validar el tipo de archivo
        $permitidos_mime = ["image/jpeg", "image/png", "image/gif"];
        if (!in_array($tipo_archivo, $permitidos_mime) || !in_array($extension, $extensiones_permitidas)) {
            $mensaje_error = "El archivo no es una imagen válida. Formatos permitidos: " . implode(', ', $extensiones_permitidas);
        }
        // Validar tamaño del archivo
        elseif ($tamano_archivo > $tamano_maximo) {
            $max_mb = $bases['tamano_maximo_mb'] ?? 10;
            $mensaje_error = "El archivo excede el tamaño máximo permitido de {$max_mb}MB.";
        }
        else {
            // Crear directorio si no existe
            $directorio = '../fotosDeUsuarios/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Generar nombre único para el archivo
            $nombre_unico = uniqid() . '.' . $extension;
            $ruta_imagen = $directorio . $nombre_unico;
            $ruta_relativa = 'fotosDeUsuarios/' . $nombre_unico;

            if (move_uploaded_file($temp_archivo, $ruta_imagen)) {
                // Guardar en la base de datos con estado "pendiente"
                $sql = "INSERT INTO imagenes (nombre_archivo, ruta, titulo, descripcion, usuario_id, estado) 
                        VALUES (?, ?, ?, ?, ?, 'pendiente')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nombre_archivo, $ruta_relativa, $titulo, $descripcion, $usuario_id);

                if ($stmt->execute()) {
                    $mensaje_exito = "Imagen subida correctamente. Está pendiente de revisión.";
                    // Actualizar contador
                    $imagenes_subidas++;
                    $imagenes_restantes = $max_imagenes - $imagenes_subidas;
                    if ($imagenes_restantes <= 0) {
                        $puede_subir = false;
                        $mensaje_error = "Has alcanzado el límite máximo de $max_imagenes imágenes para este concurso.";
                    }
                } else {
                    $mensaje_error = "Error al guardar la imagen en la base de datos.";
                }
                $stmt->close();
            } else {
                $mensaje_error = "Error al mover el archivo subido.";
            }
        }
    } else {
        $mensaje_error = "No se ha seleccionado una imagen válida o hubo un error en la subida.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen | pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../assets/logoIcon.png">
    <style>
        .card-header {
            background-color: #090643;
            color: white;
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

        .btnSubirImagen {
            background-color: #090643;
            color: white;
            transition: all 0.3s ease;
        }

        .btnSubirImagen:hover {
            background-color: #120d6b;
            color: white;
            transform: translateY(-2px);
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1rem 0;
            text-align: center;
            z-index: 1000;
        }

        .quota-badge {
            background-color: <?php echo ($imagenes_restantes > 0) ? '#28a745' : '#dc3545'; ?>;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: inline-flex;
            align-items: center;
        }

        .quota-badge .bi {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../assets/logo.png" alt="Logo pixFly" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/miPerfil.php">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/misImagenes.php">Mis Imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger" href="logout.php">
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-cloud-arrow-up me-2"></i> Subir nueva imagen</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje_exito): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i><?php echo $mensaje_exito; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($mensaje_error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $mensaje_error; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Mostrar cuota de imágenes -->
                        <div class="quota-badge mb-4">
                            <i class="bi bi-images"></i>
                            <?php if ($max_imagenes > 0): ?>
                                <?php if ($imagenes_restantes > 0): ?>
                                    Puedes subir <?php echo $imagenes_restantes; ?> de <?php echo $max_imagenes; ?> imágenes permitidas
                                <?php else: ?>
                                    Límite alcanzado: <?php echo $max_imagenes; ?> imágenes
                                <?php endif; ?>
                            <?php else: ?>
                                Puedes subir imágenes (sin límite en este momento)
                            <?php endif; ?>
                        </div>

                        <?php if ($puede_subir): ?>
                        <form action="subir_imagen.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Seleccionar imagen</label>
                                <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" required>
                                <div class="form-text">
                                    Formatos permitidos: <?php echo implode(', ', $extensiones_permitidas); ?>. 
                                    Tamaño máximo: <?php echo isset($bases['tamano_maximo_mb']) ? $bases['tamano_maximo_mb'] : 10; ?>MB.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="100">
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btnSubirImagen">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> Subir Imagen
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                No puedes subir más imágenes para este concurso. El límite es <?php echo $max_imagenes; ?> imágenes por usuario.
                            </div>
                            <a href="../InicioSesion/usuario/misImagenes.php" class="btn btn-outline-primary">
                                <i class="bi bi-images me-1"></i> Ver mis imágenes
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>