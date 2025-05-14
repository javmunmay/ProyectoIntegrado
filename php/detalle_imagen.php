<?php
session_start();
require_once 'conexion.php';
require_once 'funciones.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

$imagen_id = $_GET['id'] ?? 0;
$usuario_id = $_SESSION['user_id'];

// Obtener información de la imagen
$sql = "SELECT i.*, u.nombre as nombre_usuario, u.foto_perfil 
        FROM imagenes i 
        JOIN Usuarios u ON i.usuario_id = u.id 
        WHERE i.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $imagen_id);
$stmt->execute();
$result = $stmt->get_result();
$imagen = $result->fetch_assoc();

if (!$imagen) {
    header("Location: ../InicioSesion/usuario/misImagenes.php");
    exit();
}

// Verificar si el usuario es el dueño de la imagen
$es_propietario = ($imagen['usuario_id'] == $usuario_id);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($imagen['titulo']); ?> | pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">

    <style>
        .footer {
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 1rem 0;
            text-align: center;
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
                        <a class="nav-link" href="../InicioSesion/usuario/home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../InicioSesion/usuario/miPerfil.php">Mi Perfil</i></a>
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
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0"><?php echo htmlspecialchars($imagen['titulo']); ?></h4>
                            <?php if ($es_propietario): ?>
                                <div>
                                    <!--<a href="editar_imagen.php?id=<?php echo $imagen_id; ?>" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>-->
                                    <a href="../InicioSesion/usuario/misImagenes.php" class="btn btn-sm btn-outline-primary">Gestionar</a>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="<?php echo '../' . htmlspecialchars($imagen['ruta']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($imagen['titulo']); ?>">
                        </div>

                        <div class="d-flex align-items-center mb-4">
                            <img src="/../../assets/<?php echo htmlspecialchars($imagen['foto_perfil'] ?? '../assets/Usuario.jpg'); ?>" class="rounded-circle me-3" width="80" height="80">
                            <div>
                                <h5 class="mb-0"><?php echo htmlspecialchars($imagen['nombre_usuario']); ?></h5>
                                <small class="text-muted">Subido el <?php echo date("d/m/Y", strtotime($imagen['fecha_subida'])); ?></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p><?php echo nl2br(htmlspecialchars($imagen['descripcion'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>