<?php
session_start();
require_once 'php/conexion.php';

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
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/foto-votacion.jpg');
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
            background: linear-gradient(90deg, #090643,rgb(186, 29, 29));
        }

        .foto-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin-bottom: 25px;
        }

        .foto-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
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
<<<<<<< HEAD

=======
        
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
        .likes-count {
            font-weight: bold;
            color: #2c3e50;
            margin-left: 5px;
        }
<<<<<<< HEAD

=======
        
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
        .likes-container {
            display: flex;
            align-items: center;
            padding: 8px 20px;
            border-radius: 50px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }
<<<<<<< HEAD

        .likes-container:hover {
            background: #e9ecef;
        }

=======
        
        .likes-container:hover {
            background: #e9ecef;
        }
        
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
        .login-required-alert {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
            animation: slideIn 0.3s forwards;
            max-width: 300px;
        }

        @keyframes slideIn {
<<<<<<< HEAD
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
=======
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 40vh;
            }

            .foto-img-container {
                height: 200px;
            }
        }

        .btn-registrarse {
            background-color: #090643;
            color: white;
            padding: 7px;
        }

        .btn-registrarse:hover {
            background-color: rgb(12, 8, 89);
            color: white;
        }

        .btn-iniciosesion {
            background-color: white;
            border: solid 1px #090643;
            color: #090643;
            padding: 7px;
        }

        .btn-iniciosesion:hover {
            background-color: #090643;
            color: white;
        }

        .bg-logo{
            background-color: #090643;
        }
    </style>
</head>
<body>
<<<<<<< HEAD
    <?php include 'php/nav.php'; ?>

=======
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-primary" href="perfil.php">
                                Mi Perfil
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-registrarse" href="InicioSesion/registro.php">Registrarse</a>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="btn btn-iniciosesion" href="InicioSesion/inicioSesion.php">
                                Iniciar Sesión <i class="bi bi-box-arrow-in-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16

    <section class="hero-section">
        <div class="hero-content">
            <h1 class="display-4 fw-bold">Vota por tus fotos favoritas</h1>
            <p class="lead">Descubre increíbles fotografías y apoya a tus artistas preferidos</p>
            <a href="#fotos" class="btn btn-lg mt-3 text-white" style="background-color: #090643;">Explorar Fotos</a>
        </div>
    </section>

    <section class="container py-5" id="fotos">
        <h2 class="text-center categoria-titulo">Fotografías en concurso</h2>
<<<<<<< HEAD

=======
        
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
        <div class="row">
            <?php if (!empty($imagenes)): ?>
                <?php foreach ($imagenes as $imagen): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card foto-card">
                            <div class="foto-img-container">
                                <img src="<?php echo htmlspecialchars($imagen['ruta']); ?>" class="foto-img"
                                    alt="<?php echo htmlspecialchars($imagen['titulo']); ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($imagen['titulo']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($imagen['descripcion']); ?></p>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="user-info">
                                        <img src="assets/<?php echo $imagen['foto_perfil'] ? htmlspecialchars($imagen['foto_perfil']) : 'assets/user-default.jpg'; ?>"
                                            alt="<?php echo htmlspecialchars($imagen['usuario_nombre']); ?>"
                                            class="user-avatar">
                                        <span><?php echo htmlspecialchars($imagen['usuario_nombre']); ?></span>
                                    </div>
<<<<<<< HEAD

                                    <div class="likes-container" onclick="mostrarLoginRequired()">
                                        <i class="bi bi-heart-fill text-danger"></i>
=======
                                    
                                    <div class="likes-container" onclick="mostrarLoginRequired()">
                                        <i class="bi bi-heart-fill text-danger"></i> 
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
                                        <span class="likes-count" data-imagen-id="<?php echo $imagen['imagen_id']; ?>">
                                            <?php echo $imagen['likes']; ?>
                                        </span>
                                    </div>
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
                            <div class="bg-logo bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px; margin-bottom: 1rem;">
                                <i class="bi bi-camera fs-4"></i>
                            </div>
                            <h4>Sube tus fotos</h4>
                            <p class="text-muted">Regístrate y comparte tus mejores fotografías para participar en el
                                concurso.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-logo bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px; margin-bottom: 1rem;">
                                <i class="bi bi-heart fs-4"></i>
                            </div>
                            <h4>Recibe votos</h4>
                            <p class="text-muted">La comunidad votará por las fotos que más les gusten. ¡Entre más
                                votos, más posibilidades de ganar!</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-logo bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px; margin-bottom: 1rem;">
                                <i class="bi bi-trophy fs-4"></i>
                            </div>
                            <h4>Gana premios</h4>
                            <p class="text-muted">Las fotos con más votos al final del concurso ganarán increíbles
                                premios.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para mostrar mensaje de login requerido
        function mostrarLoginRequired() {
            // Eliminar notificaciones anteriores
            const oldAlerts = document.querySelectorAll('.login-required-alert');
            oldAlerts.forEach(alert => alert.remove());
<<<<<<< HEAD

=======
            
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
            // Crear nueva notificación
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning login-required-alert alert-dismissible fade show';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>Debes iniciar sesión para votar</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <div class="mt-2">
                    <a href="InicioSesion/inicioSesion.php" class="btn btn-sm btn-primary">Iniciar Sesión</a>
                </div>
            `;
<<<<<<< HEAD

            document.body.appendChild(alertDiv);

=======
            
            document.body.appendChild(alertDiv);
            
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
            // Eliminar automáticamente después de 5 segundos
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Función para actualizar los likes
        async function actualizarLikes() {
            try {
                const response = await fetch('php/obtener_likes.php');
                const data = await response.json();
<<<<<<< HEAD

=======
                
>>>>>>> a6a454e509466c7c9148bd80b4cf40c5e9be3c16
                if (data.success) {
                    // Actualizar todos los contadores de likes
                    data.likes.forEach(imagen => {
                        const likeElement = document.querySelector(`.likes-count[data-imagen-id="${imagen.id}"]`);
                        if (likeElement) {
                            likeElement.textContent = imagen.likes;
                        }
                    });
                }
            } catch (error) {
                console.error('Error al actualizar likes:', error);
            }
        }

        // Actualizar likes cada 5 segundos
        setInterval(actualizarLikes, 5000);

        // También actualizar al cargar la página
        document.addEventListener('DOMContentLoaded', actualizarLikes);
    </script>
</body>

</html>