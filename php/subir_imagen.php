<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['user_id'];
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['imagen']['name'];
        $tipo_archivo = $_FILES['imagen']['type'];
        $tamano_archivo = $_FILES['imagen']['size'];
        $temp_archivo = $_FILES['imagen']['tmp_name'];

        // Validar que sea una imagen
        $permitidos = array("image/jpeg", "image/png", "image/gif");
        if (in_array($tipo_archivo, $permitidos)) {
            // Crear directorio si no existe
            $directorio = '../fotosDeUsuarios/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Mover el archivo
            $ruta_imagen = $directorio . $nombre_archivo;
            $ruta_imagen2 = 'fotosDeUsuarios/' . $nombre_archivo;
            if (move_uploaded_file($temp_archivo, $ruta_imagen)) {
                // Guardar en la base de datos con estado "pendiente"
                $sql = "INSERT INTO imagenes (nombre_archivo, ruta, titulo, descripcion, usuario_id, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nombre_archivo, $ruta_imagen2, $titulo, $descripcion, $usuario_id);

                if ($stmt->execute()) {
                    header("Location: ../InicioSesion/usuario/misImagenes.php?success=1");
                } else {
                    header("Location: subir_imagen.php?error=1");
                }
                exit();
            }
        }
    }

    header("Location: subir_imagen.php?error=2");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Imagen | pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylesIndex.css">
    <style>
        .card-header {
            background-color: #2a3d74;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="../InicioSesion/usuario/home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/miPerfil.php">Mi Perfil</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/misImagenes.php">Mis Imágenes</a>
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
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-cloud-arrow-up"></i> Subir nueva imagen</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger">
                                <?php
                                if ($_GET['error'] == 1) echo "Error al guardar la imagen en la base de datos.";
                                elseif ($_GET['error'] == 2) echo "El archivo no es una imagen válida o no se pudo subir.";
                                ?>
                            </div>
                        <?php endif; ?>

                        <form action="subir_imagen.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Seleccionar imagen</label>
                                <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>

                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-arrow-up"></i> Subir Imagen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>