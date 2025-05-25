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

// Obtener las bases del concurso activo (con recarga forzada)
$sql_bases = "SELECT * FROM bases_concurso 
              WHERE fecha_inicio_concurso <= CURDATE() 
              AND fecha_fin_concurso >= CURDATE() 
              ORDER BY id DESC LIMIT 1";

$result_bases = $conn->query($sql_bases);
<<<<<<< HEAD
=======

<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
$concurso_activo = ($result_bases->num_rows > 0);


<<<<<<< HEAD
// Valores por defecto (cuando no hay concurso activo)
$max_imagenes = 5;
$extensiones_permitidas = ['jpg', 'jpeg', 'png'];
$tamano_maximo = 10 * 1024 * 1024; // 10MB en bytes

if ($concurso_activo) {
    //echo "Prueba de curso activo";
=======
<<<<<<< HEAD
=======
=======
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
if ($result_bases->num_rows > 0) {
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
    $bases = $result_bases->fetch_assoc();
    $max_imagenes = (int)$bases['max_imagenes_por_usuario'];
    $extensiones_permitidas = explode(',', $bases['extensiones_permitidas']);
    $tamano_maximo = (int)$bases['tamano_maximo_mb'] * 1024 * 1024;
}

<<<<<<< HEAD
// Verificar imágenes subidas (siempre con consulta fresca)
$sql_imagenes_usuario = "SELECT COUNT(*) as total FROM imagenes 
    WHERE usuario_id = ? AND estado IN ('activo', 'pendiente')";
$stmt_imagenes = $conn->prepare($sql_imagenes_usuario);
$stmt_imagenes->bind_param("i", $usuario_id);
$stmt_imagenes->execute();
$result_imagenes = $stmt_imagenes->get_result();
$imagenes_subidas = (int)$result_imagenes->fetch_assoc()['total'];
$stmt_imagenes->close();
=======
    // Verificar cuántas imágenes ha subido el usuario (activas y pendientes)
    $sql_imagenes_usuario = "SELECT COUNT(*) as total FROM imagenes 
        WHERE usuario_id = ? AND estado IN ('activo', 'pendiente')";
=======
<<<<<<< HEAD
    $tamano_maximo = $bases['tamano_maximo_mb'] * 1024 * 1024;

    // Verificar cuántas imágenes ha subido el usuario (activas y pendientes)
    $sql_imagenes_usuario = "SELECT COUNT(*) as total FROM imagenes 
        WHERE usuario_id = ? AND estado IN ('activo', 'pendiente')";
=======
    $tamano_maximo = $bases['tamano_maximo_mb'] * 1024 * 1024; // Convertir MB a bytes
    
    // Verificar cuántas imágenes ha subido el usuario (sin importar el estado)
    $sql_imagenes_usuario = "SELECT COUNT(*) as total FROM imagenes 
                            WHERE usuario_id = ?";
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
    $stmt_imagenes = $conn->prepare($sql_imagenes_usuario);
    $stmt_imagenes->bind_param("i", $usuario_id);
    $stmt_imagenes->execute();
    $result_imagenes = $stmt_imagenes->get_result();
    $imagenes_subidas = $result_imagenes->fetch_assoc()['total'];
    $stmt_imagenes->close();
<<<<<<< HEAD
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16

// Calcular disponibilidad
$imagenes_restantes = $max_imagenes - $imagenes_subidas;
$puede_subir = ($imagenes_restantes > 0);

<<<<<<< HEAD
if (!$puede_subir) {
    $mensaje_error = $concurso_activo
        ? "Has alcanzado el límite máximo de $max_imagenes imágenes para este concurso."
        : "Tienes $imagenes_subidas imágenes. Por favor, elimina algunas antes de subir más.";
=======
=======
<<<<<<< HEAD

    $imagenes_restantes = $max_imagenes - $imagenes_subidas;

=======
    
    $imagenes_restantes = $max_imagenes - $imagenes_subidas;
    
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
    if ($imagenes_restantes <= 0) {
        $puede_subir = false;
        $mensaje_error = "Has alcanzado el límite máximo de $max_imagenes imágenes para este concurso.";
    }
} else {
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
    // Si no hay concurso activo, verificar si hay más de 5 imágenes para mostrar advertencia
    $sql_total_imagenes = "SELECT COUNT(*) as total FROM imagenes 
        WHERE usuario_id = ? AND estado IN ('activo', 'pendiente')";
    $stmt_total = $conn->prepare($sql_total_imagenes);
    $stmt_total->bind_param("i", $usuario_id);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total_imagenes = $result_total->fetch_assoc()['total'];
    $stmt_total->close();

    if ($total_imagenes > 5) {
        $puede_subir = false;
        $mensaje_error = "Tienes $total_imagenes imágenes. Por favor, elimina algunas antes de subir más.";
    } else {
        // Si no hay concurso y tiene menos de 5 imágenes, permitir subida sin límites
        $max_imagenes = 0;
        $imagenes_restantes = 1;
        $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
        $tamano_maximo = 10 * 1024 * 1024;
    }
<<<<<<< HEAD
=======
=======
    // Si no hay concurso activo, permitir subida sin límites
    $max_imagenes = 0;
    $imagenes_restantes = 1; // Para que siempre muestre que puede subir
    $extensiones_permitidas = ['jpg', 'jpeg', 'png'];
    $tamano_maximo = 10 * 1024 * 1024; // 10MB por defecto
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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
        } else {
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
        } else {
=======
        }
        else {
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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

// Obtener todas las imágenes del usuario para el modal de eliminación si es necesario
$imagenes_usuario = [];
if (!$puede_subir && $max_imagenes > 0) {
    $sql_imagenes = "SELECT id, ruta, titulo FROM imagenes 
                    WHERE usuario_id = ? AND estado IN ('activo', 'pendiente') 
                    ORDER BY fecha_subida DESC";
    $stmt = $conn->prepare($sql_imagenes);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $imagenes_usuario = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen | pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
            background-color:
                <?php echo ($imagenes_restantes > 0) ? '#28a745' : '#dc3545'; ?>;
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
            background-color:
                <?php echo ($imagenes_restantes > 0) ? '#28a745' : '#dc3545'; ?>;
=======
            background-color: <?php echo ($imagenes_restantes > 0) ? '#28a745' : '#dc3545'; ?>;
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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

        /* Estilos para el modal de advertencia */
        .modal-advertencia .modal-header {
            background-color: #dc3545;
            color: white;
        }

        .imagen-eliminar {
            position: relative;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            transition: all 0.3s;
        }

        .imagen-eliminar img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 3px;
        }

        .imagen-eliminar .form-check {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .imagen-eliminar .titulo {
            margin-top: 5px;
            font-weight: 500;
            text-align: center;
        }

        .contador-eliminar {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .imagen-seleccionada {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .btn-subir-deshabilitado {
            opacity: 0.5;
            pointer-events: none;
        }

        .nav-link.disabled {
            color: #6c757d !important;
            pointer-events: none;
        }

        .carousel-item img {
            object-fit: cover;
            height: 100vh;
            min-height: 600px;
            filter: brightness(0.6);
        }

        .image-scroll-container {
            overflow-x: auto;
            padding: 15px 0;
            width: 100%;
            -webkit-overflow-scrolling: touch;
            /* Para un scroll suave en móviles */
        }

        .image-scroll {
            display: flex;
            gap: 15px;
            padding: 0 15px;
            /* Añade padding para que no pegue a los bordes */
        }

        .image-scroll img {
            height: 250px;
            min-width: auto;
            /* Evita que las imágenes se compriman */
            max-width: none;
            /* Permite que las imágenes mantengan su proporción */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            object-fit: cover;
            /* Asegura que las imágenes mantengan su relación de aspecto */
            flex: 0 0 auto;
            /* Evita que las imágenes se estiren o encojan */
        }

        /* Estilos para el modal de advertencia */
        .modal-advertencia .modal-header {
            background-color: #dc3545;
            color: white;
        }

        .imagen-eliminar {
            position: relative;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            transition: all 0.3s;
        }

        .imagen-eliminar img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 3px;
        }

        .imagen-eliminar .form-check {
            position: absolute;
            top: 10px;
            left: 10px;
        }

        .imagen-eliminar .titulo {
            margin-top: 5px;
            font-weight: 500;
            text-align: center;
        }

        .contador-eliminar {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
        }

        .imagen-seleccionada {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .btn-subir-deshabilitado {
            opacity: 0.5;
            pointer-events: none;
        }

        .nav-link.disabled {
            color: #6c757d !important;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../InicioSesion/usuario/home.php">
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
            <a class="navbar-brand" href="../InicioSesion/usuario/home.php">
=======
            <a class="navbar-brand" href="home.php">
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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
                        <a class="nav-link <?php echo !$puede_subir ? 'disabled' : ''; ?>"
                            href="votacion.php">Votación</a>
<<<<<<< HEAD
=======
=======
<<<<<<< HEAD
                        <a class="nav-link <?php echo !$puede_subir ? 'disabled' : ''; ?>"
                            href="votacion.php">Votación</a>
=======
                        <a class="nav-link" href="votacion.php">Votación</a>
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
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
<<<<<<< HEAD
                            </div>
                        <?php endif; ?>


=======
=======
                            </div>
                        <?php endif; ?>

                        <?php if ($mensaje_error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $mensaje_error; ?>
                            </div>
                        <?php endif; ?>

                        <div class="quota-badge mb-4">
                            <i class="bi bi-images"></i>
                            <?php if ($imagenes_restantes > 0): ?>
                                Puedes subir <?php echo $imagenes_restantes; ?> de <?php echo $max_imagenes; ?> imágenes permitidas
                                <?php echo $concurso_activo ? 'para el concurso' : ''; ?>
                            <?php else: ?>
                                Límite <?php echo $concurso_activo ? 'del concurso' : 'general'; ?> alcanzado: <?php echo $max_imagenes; ?> imágenes
                            <?php endif; ?>
                        </div>

                        <?php if ($puede_subir): ?>
                            <form action="subir_imagen.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="imagen" class="form-label">Seleccionar imagen</label>
                                    <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*"
                                        required>
                                    <div class="form-text">
                                        Formatos permitidos: <?php echo implode(', ', $extensiones_permitidas); ?>.
                                        Tamaño máximo:
                                        <?php echo isset($bases['tamano_maximo_mb']) ? $bases['tamano_maximo_mb'] : 10; ?>MB.
                                    </div>
                                </div>
<<<<<<< HEAD

=======
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
                            </div>
                        <?php endif; ?>

<<<<<<< HEAD
                        <div class="quota-badge mb-4">
                            <i class="bi bi-images"></i>
                            <?php if ($imagenes_restantes > 0): ?>
                                Puedes subir <?php echo $imagenes_restantes; ?> de <?php echo $max_imagenes; ?> imágenes permitidas
                                <?php echo $concurso_activo ? 'para el concurso' : ''; ?>
                            <?php else: ?>
                                Límite <?php echo $concurso_activo ? 'del concurso' : 'general'; ?> alcanzado: <?php echo $max_imagenes; ?> imágenes
                            <?php endif; ?>
                        </div>
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16

                        <?php if ($puede_subir): ?>
                            <form action="subir_imagen.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="imagen" class="form-label">Seleccionar imagen</label>
                                    <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*"
                                        required>
                                    <div class="form-text">
                                        Formatos permitidos: <?php echo implode(', ', $extensiones_permitidas); ?>.
                                        Tamaño máximo:
                                        <?php echo isset($bases['tamano_maximo_mb']) ? $bases['tamano_maximo_mb'] : 10; ?>MB.
                                    </div>
                                </div>

>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required
                                        maxlength="100">
                                </div>
<<<<<<< HEAD
=======

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                        maxlength="500"></textarea>
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
                                No puedes subir más imágenes para este concurso. Por favor borra las imágenes necesarias para subir más imágenes.
                            </div>
<<<<<<< HEAD
=======
=======
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="100">
                            </div>
>>>>>>> 74bfcde31890af4f6ff0a444e1071ee52fa1fefb

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                        maxlength="500"></textarea>
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
                                No puedes subir más imágenes para este concurso. El límite es <?php echo $max_imagenes; ?>
                                imágenes por usuario.
                            </div>
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
                            <a href="../InicioSesion/usuario/misImagenes.php" class="btn btn-outline-primary">
                                <i class="bi bi-images me-1"></i> Ver mis imágenes
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="destacadas" class="container mt-5">
        <h2 class="text-center mb-4 categoria-titulo">Inspiración total</h2>
        <div class="image-scroll-container">
            <div class="image-scroll">
                <!-- PHP incluiría las imágenes más votadas -->
                <img src="../assets/foto1.jpg" alt="Fotografía destacada 1">
                <img src="../assets/foto2.jpg" alt="Fotografía destacada 2">
                <img src="../assets/foto3.jpg" alt="Fotografía destacada 3">
                <img src="../assets/foto4.jpg" alt="Fotografía destacada 4">
                <img src="../assets/foto5.jpg" alt="Fotografía destacada 5">
            </div>
        </div>
    </section>

    <br><br>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar modal de advertencia si se excede el límite
        <?php if (!$puede_subir && $max_imagenes > 0): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var advertenciaModal = new bootstrap.Modal(document.getElementById('advertenciaModal'));
                advertenciaModal.show();

                // Variables de control
                const checkboxes = document.querySelectorAll('.checkbox-eliminar');
                const contador = document.querySelector('.contador-eliminar');
                const btnEliminar = document.getElementById('btnEliminarSeleccionadas');
                const imagenesAEliminar = <?php echo abs($imagenes_restantes); ?>;

                // Función para actualizar el estado
                function actualizarEstado() {
                    const seleccionadas = document.querySelectorAll('.checkbox-eliminar:checked').length;
                    const restantes = Math.max(0, imagenesAEliminar - seleccionadas);

                    contador.textContent = restantes;
                    btnEliminar.disabled = restantes > 0;

                    // Resaltar imágenes seleccionadas
                    checkboxes.forEach(checkbox => {
                        const card = checkbox.closest('.imagen-eliminar');
                        if (checkbox.checked) {
                            card.classList.add('imagen-seleccionada');
                        } else {
                            card.classList.remove('imagen-seleccionada');
                        }
                    });
                }

                // Event listeners
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', actualizarEstado);
                });

                // Validar antes de enviar el formulario
                document.getElementById('formEliminarImagenes').addEventListener('submit', function(e) {
                    const seleccionadas = document.querySelectorAll('.checkbox-eliminar:checked').length;
                    if (seleccionadas < imagenesAEliminar) {
                        e.preventDefault();
                        alert(`Debes seleccionar al menos ${imagenesAEliminar} imágenes para eliminar.`);
                    }
                });
            });
        <?php endif; ?>
    </script>
</body>

</html>