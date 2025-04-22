<?php
session_start();
require_once '../../php/conexion.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: InicioSesion/inicioSesion.php");
    exit();
}

// Obtener datos del usuario
$usuario_id = $_SESSION['user_id'];
$nombreUsuario = $_SESSION['user_nombre'];

// Consultas a la base de datos
// 1. Número de imágenes subidas por el usuario
$sql_user_images = "SELECT COUNT(*) as total FROM imagenes WHERE usuario_id = ? AND estado = 'activo'";
$stmt_user = $conn->prepare($sql_user_images);
$stmt_user->bind_param("i", $usuario_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_images = $result_user->fetch_assoc()['total'];
$stmt_user->close();

// 2. Número total de imágenes en la plataforma
$sql_total_images = "SELECT COUNT(*) as total FROM imagenes WHERE estado = 'activo'";
$result_total = $conn->query($sql_total_images);
$total_images = $result_total->fetch_assoc()['total'];

// 3. Obtener imágenes para el feed (últimas 20 imágenes activas)
$sql_feed = "SELECT i.id, i.nombre_archivo, i.ruta, i.titulo, i.descripcion, 
             u.nombre as usuario_nombre, u.foto_perfil as usuario_foto
             FROM imagenes i
             JOIN Usuarios u ON i.usuario_id = u.id
             WHERE i.estado = 'activo'
             ORDER BY i.fecha_subida DESC
             LIMIT 20";
$result_feed = $conn->query($sql_feed);
$feed_images = [];
if ($result_feed->num_rows > 0) {
    while($row = $result_feed->fetch_assoc()) {
        $feed_images[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../css/stylesIndex.css">
    <link rel="icon" type="image/png" href="../../assets/logoIcon.png">
    <style>
        .stats-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .feed-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .post-card {
            background: white;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }
        
        .post-header {
            padding: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        
        .post-user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .post-image {
            width: 100%;
            max-height: 600px;
            object-fit: contain;
        }
        
        .post-body {
            padding: 15px;
        }
        
        .post-actions {
            padding: 10px 15px;
            border-top: 1px solid #eee;
        }
        
        .action-icon {
            font-size: 24px;
            margin-right: 15px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="miPerfil.php">Mi Perfil</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="misImagenes.php">Mis Imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger" href="../../php/logout.php">
                            Cerrar Sesión 
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h4>Bienvenido</h4>
                    <h3 class="text-primary"><?php echo htmlspecialchars($nombreUsuario); ?></h3>
                    <img src="<?php echo $_SESSION['user_foto']; ?>" alt="Foto de perfil" class="img-thumbnail rounded-circle mt-3" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h4>Mis imágenes</h4>
                    <h2 class="text-success"><?php echo $user_images; ?></h2>
                    <p class="text-muted">subidas a la plataforma</p>
                    <a href="misImagenes.php" class="btn btn-sm btn-outline-success">Ver mis imágenes</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card text-center">
                    <h4>Total en pixFly</h4>
                    <h2 class="text-info"><?php echo $total_images; ?></h2>
                    <p class="text-muted">imágenes compartidas</p>
                    <a href="../votacion.php" class="btn btn-sm btn-outline-info">Ver todas</a>
                </div>
            </div>
        </div>
    </div>

    <section class="container mt-5">
        <h2 class="text-center mb-4">Feed de Imágenes</h2>
        <div class="feed-container">
            <?php if (!empty($feed_images)): ?>
                <?php foreach ($feed_images as $image): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <img src="<?php echo !empty($image['usuario_foto']) ? $image['usuario_foto'] : '../../assets/Usuario.jpg'; ?>" 
                                 alt="Usuario" class="post-user-img">
                            <strong><?php echo htmlspecialchars($image['usuario_nombre']); ?></strong>
                        </div>
                        <img src="<?php echo '../../' . $image['ruta']; ?>" alt="<?php echo htmlspecialchars($image['titulo']); ?>" class="post-image">
                        <div class="post-body">
                            <h5><?php echo htmlspecialchars($image['titulo']); ?></h5>
                            <p><?php echo htmlspecialchars($image['descripcion']); ?></p>
                        </div>
                        <div class="post-actions">
                            <i class="bi bi-heart action-icon text-danger"></i>
                            <i class="bi bi-chat action-icon text-muted"></i>
                            <i class="bi bi-share action-icon text-muted"></i>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    No hay imágenes para mostrar. ¡Sé el primero en subir una!
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Interacción básica para los likes
        document.querySelectorAll('.bi-heart').forEach(icon => {
            icon.addEventListener('click', function() {
                if (this.classList.contains('text-danger')) {
                    this.classList.remove('text-danger');
                    this.classList.add('text-muted');
                } else {
                    this.classList.remove('text-muted');
                    this.classList.add('text-danger');
                }
            });
        });
    </script>
</body>
</html>