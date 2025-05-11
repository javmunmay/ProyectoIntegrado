<?php
session_start();
require_once 'php/conexion.php';

// Verificar si el usuario está logueado
$usuario_logueado = isset($_SESSION['user_id']);

// Consulta para obtener las imágenes activas con información del usuario
$sql_imagenes = "SELECT 
                i.id AS imagen_id,
                i.ruta,
                i.titulo,
                i.descripcion,
                i.likes,
                u.id AS usuario_id,
                u.nombre AS usuario_nombre,
                u.foto_perfil
            FROM 
                imagenes i
            JOIN 
                Usuarios u ON i.usuario_id = u.id
            WHERE 
                i.estado = 'activo'
            ORDER BY 
                i.likes DESC";

$result_imagenes = $conn->query($sql_imagenes);
$imagenes = [];
if ($result_imagenes->num_rows > 0) {
    while ($row = $result_imagenes->fetch_assoc()) {
        $imagenes[] = $row;
    }
}

// Procesar votos si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['votar']) {
    if ($usuario_logueado) {
        $imagen_id = intval($_POST['imagen_id']);
        
        // Actualizar los likes en la base de datos
        $sql_votar = "UPDATE imagenes SET likes = likes + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql_votar);
        $stmt->bind_param("i", $imagen_id);
        
        if ($stmt->execute()) {
            // Actualizar la lista de imágenes después del voto
            header("Location: votacion.php");
            exit();
        } else {
            $error_voto = "Error al registrar el voto";
        }
    } else {
        $error_voto = "Debes iniciar sesión para votar";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votación - PixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
    <style>
        .hero-section {
            position: relative;
            height: 50vh;
            min-height: 400px;
            overflow: hidden;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/foto-votacion.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }
        
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 80%;
        }
        
        .categoria-titulo {
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        
        .categoria-titulo:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #9b59b6);
        }
        
        .foto-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 25px;
        }
        
        .foto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .foto-img-container {
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .foto-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .foto-card:hover .foto-img {
            transform: scale(1.05);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            border: 2px solid #eee;
        }
        
        .votar-btn {
            background: linear-gradient(90deg, #3498db, #9b59b6);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .votar-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .votar-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        .likes-count {
            font-weight: bold;
            color: #2c3e50;
            margin-left: 5px;
        }
        
        .login-alert {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            animation: slideIn 0.5s forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        
        @media (max-width: 768px) {
            .hero-section {
                height: 40vh;
            }
            
            .foto-img-container {
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="assets/logo.png" alt="Logo PixFly" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ganadores.php">Ganadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <?php if ($usuario_logueado): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-primary" href="perfil.php">
                                Mi Perfil
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-primary" href="InicioSesion/registro.php">Registrarse</a>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="btn btn-outline-primary" href="InicioSesion/inicioSesion.php">
                                Iniciar Sesión <i class="bi bi-box-arrow-in-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-content">
            <h1 class="display-4 fw-bold">Vota por tus fotos favoritas</h1>
            <p class="lead">Descubre increíbles fotografías y apoya a tus artistas preferidos</p>
            <a href="#fotos" class="btn btn-primary btn-lg mt-3">Explorar Fotos</a>
        </div>
    </section>

    <section class="container py-5" id="fotos">
        <h2 class="text-center categoria-titulo">Fotografías en concurso</h2>
        
        <?php if (isset($error_voto)): ?>
            <div class="alert alert-danger"><?php echo $error_voto; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <?php if (!empty($imagenes)): ?>
                <?php foreach ($imagenes as $imagen): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card foto-card">
                            <div class="foto-img-container">
                                <img src="<?php echo htmlspecialchars($imagen['ruta']); ?>" class="foto-img" alt="<?php echo htmlspecialchars($imagen['titulo']); ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($imagen['titulo']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($imagen['descripcion']); ?></p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="user-info">
                                        <img src="<?php echo $imagen['foto_perfil'] ? htmlspecialchars($imagen['foto_perfil']) : 'assets/user-default.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($imagen['usuario_nombre']); ?>" class="user-avatar">
                                        <span><?php echo htmlspecialchars($imagen['usuario_nombre']); ?></span>
                                    </div>
                                    
                                    <form method="POST" action="votacion.php">
                                        <input type="hidden" name="imagen_id" value="<?php echo $imagen['imagen_id']; ?>">
                                        <button type="submit" name="votar" class="votar-btn" <?php echo !$usuario_logueado ? 'disabled' : ''; ?>>
                                            <i class="bi bi-heart-fill"></i> 
                                            <span class="likes-count"><?php echo $imagen['likes']; ?></span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card py-5">
                        <h3>No hay fotos para mostrar todavía</h3>
                        <p class="mb-4">Sube tus fotos y participa en el concurso</p>
                        <a href="InicioSesion/inicioSesion.php" class="btn btn-primary">Inicia Sesión ahora</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center categoria-titulo">Cómo participar</h2>
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; margin-bottom: 1rem;">
                                <i class="bi bi-camera fs-4"></i>
                            </div>
                            <h4>Sube tus fotos</h4>
                            <p class="text-muted">Regístrate y comparte tus mejores fotografías para participar en el concurso.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; margin-bottom: 1rem;">
                                <i class="bi bi-heart fs-4"></i>
                            </div>
                            <h4>Recibe votos</h4>
                            <p class="text-muted">La comunidad votará por las fotos que más les gusten. ¡Entre más votos, más posibilidades de ganar!</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; margin-bottom: 1rem;">
                                <i class="bi bi-trophy fs-4"></i>
                            </div>
                            <h4>Gana premios</h4>
                            <p class="text-muted">Las fotos con más votos al final del concurso ganarán increíbles premios.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <img src="assets/logo-white.png" alt="PixFly" style="height: 40px; margin-bottom: 15px;">
                    <p>Plataforma líder en concursos de fotografía digital desde 2010.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i> info@pixfly.com</li>
                        <li><i class="bi bi-phone me-2"></i> +34 123 456 789</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Madrid, España</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="text-white fs-4"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0 small">© 2025 PixFly. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <?php if (!$usuario_logueado): ?>
        <div class="login-alert alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Inicia sesión</strong> para poder votar por tus fotos favoritas.
            <a href="InicioSesion/inicioSesion.php" class="alert-link">Ingresar ahora</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efecto suave al hacer clic en votar
        document.querySelectorAll('.votar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.disabled) {
                    this.innerHTML = '<i class="bi bi-heart-fill"></i> Gracias!';
                    this.classList.add('btn-success');
                    this.classList.remove('bg-gradient');
                    
                    // Actualizar contador
                    const countElement = this.querySelector('.likes-count');
                    let count = parseInt(countElement.textContent);
                    countElement.textContent = count + 1;
                    
                    // Deshabilitar después de votar
                    this.disabled = true;
                }
            });
        });
        
        // Cerrar alerta de login
        document.querySelector('.btn-close')?.addEventListener('click', function() {
            this.closest('.alert').style.display = 'none';
        });
    </script>
</body>
</html>