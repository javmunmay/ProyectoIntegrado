<?php
session_start();
require_once 'php/conexion.php';

// Consulta para obtener los 3 usuarios con más likes
$sql_top3 = "SELECT 
            u.id AS user_id,
            u.nombre AS username,
            u.foto_perfil,
            SUM(i.likes) AS total_likes,
            COUNT(i.id) AS total_fotos
        FROM 
            Usuarios u
        LEFT JOIN 
            imagenes i ON u.id = i.usuario_id
        WHERE 
            i.estado = 'activo'
        GROUP BY 
            u.id
        HAVING 
            total_likes > 0
        ORDER BY 
            total_likes DESC
        LIMIT 3";

$result_top3 = $conn->query($sql_top3);
$ganadores = [];
if ($result_top3->num_rows > 0) {
    while ($row = $result_top3->fetch_assoc()) {
        $ganadores[] = $row;
    }
}

// Consulta para estadísticas generales
$sql_stats = "SELECT 
            COUNT(DISTINCT u.id) AS total_participantes,
            COUNT(i.id) AS total_fotos_subidas,
            SUM(i.likes) AS total_likes_concursantes,
            (SELECT COUNT(id) FROM Usuarios) AS total_usuarios_registrados
        FROM 
            Usuarios u
        LEFT JOIN 
            imagenes i ON u.id = i.usuario_id
        WHERE 
            i.estado = 'activo'";

$stats = $conn->query($sql_stats)->fetch_assoc();

// Consulta para top 5 participantes
$sql_top5 = "SELECT 
            u.nombre AS username,
            SUM(i.likes) AS total_likes
        FROM 
            Usuarios u
        JOIN 
            imagenes i ON u.id = i.usuario_id
        WHERE 
            i.estado = 'activo'
        GROUP BY 
            u.id
        ORDER BY 
            total_likes DESC
        LIMIT 5";
$result_top5 = $conn->query($sql_top5);
$top5 = $result_top5->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganadores - PixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
    <style>
        .hero-section {
            position: relative;
            height: 60vh;
            min-height: 400px;
            overflow: hidden;
        }
        
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 2;
            width: 80%;
        }
        
        .hero-bg {
            object-fit: cover;
            height: 100%;
            width: 100%;
            filter: brightness(0.6);
            position: absolute;
            top: 0;
            left: 0;
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
        
        .logo {
            transition: transform 0.3s;
        }
        
        .logo:hover {
            transform: scale(1.05);
        }
        
        .premio-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .premio-card:hover {
            transform: translateY(-10px);
        }
        
        .premio-icon {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 1rem;
        }
        
        .ganador-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .ganador-card:hover {
            transform: translateY(-5px);
        }
        
        .ganador-img {
            height: 250px;
            object-fit: cover;
        }
        
        .medalla {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .medalla-oro {
            background: linear-gradient(135deg, #FFD700, #D4AF37);
        }
        
        .medalla-plata {
            background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
        }
        
        .medalla-bronce {
            background: linear-gradient(135deg, #CD7F32, #B87333);
        }
        
        .stat-card {
            border: none;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            color: #9b59b6;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .progress-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .countdown-container {
            margin-top: 30px;
        }
        
        .countdown-item {
            display: inline-block;
            margin: 0 10px;
            text-align: center;
        }
        
        .countdown-number {
            font-size: 2rem;
            font-weight: bold;
            color: white;
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            min-width: 80px;
        }
        
        .countdown-label {
            font-size: 0.9rem;
            margin-top: 5px;
            display: block;
            color: rgba(255,255,255,0.8);
        }
        
        @media (max-width: 768px) {
            .hero-section {
                height: 50vh;
            }
            
            .countdown-number {
                font-size: 1.5rem;
                min-width: 60px;
                padding: 8px 15px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="assets/logo.png" alt="Logo PixFly" class="logo" style="height: 50px;">
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
                        <a class="nav-link" href="votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ganadores.php">Ganadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary" href="InicioSesion/registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2">
                        <a class="btn btn-outline-primary" href="InicioSesion/inicioSesion.php">
                            Iniciar Sesión <i class="bi bi-key"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <img src="assets/foto1.jpg" class="hero-bg" alt="Ganadores PixFly">
        <div class="hero-content">
            <h1 class="display-4 fw-bold">Ganadores del Concurso 2025</h1>
            <p class="lead">Descubre a los fotógrafos más votados y los increíbles premios en juego</p>
            
            <div class="countdown-container">
                <p>Tiempo restante para el cierre del concurso:</p>
                <div id="countdown">
                    <div class="countdown-item">
                        <span class="countdown-number" id="days">00</span>
                        <span class="countdown-label">Días</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="hours">00</span>
                        <span class="countdown-label">Horas</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="minutes">00</span>
                        <span class="countdown-label">Minutos</span>
                    </div>
                    <div class="countdown-item">
                        <span class="countdown-number" id="seconds">00</span>
                        <span class="countdown-label">Segundos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <h2 class="text-center categoria-titulo">Premios del Concurso</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card premio-card h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-trophy-fill premio-icon"></i>
                        <h3>1er Premio</h3>
                        <p class="text-muted">Cámara profesional Nikon Z6 II con lente 24-70mm</p>
                        <h4 class="text-primary">2.800€</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card premio-card h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-laptop premio-icon"></i>
                        <h3>2do Premio</h3>
                        <p class="text-muted">Ordenador MacBook Pro para edición de fotos</p>
                        <h4 class="text-primary">1.999€</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card premio-card h-100">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-airplane-engines premio-icon"></i>
                        <h3>3er Premio</h3>
                        <p class="text-muted">Billetes de avión para un viaje en pareja a Europa</p>
                        <h4 class="text-primary">1.200€</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center categoria-titulo">Estadísticas del Concurso</h2>
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <i class="bi bi-people-fill stat-icon"></i>
                        <div class="stat-number"><?php echo $stats['total_participantes']; ?></div>
                        <p>Participantes</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <i class="bi bi-images stat-icon"></i>
                        <div class="stat-number"><?php echo $stats['total_fotos_subidas']; ?></div>
                        <p>Fotos subidas</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <i class="bi bi-heart-fill stat-icon"></i>
                        <div class="stat-number"><?php echo number_format($stats['total_likes_concursantes']); ?></div>
                        <p>Likes totales</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="stat-card">
                        <i class="bi bi-person-plus-fill stat-icon"></i>
                        <div class="stat-number"><?php echo $stats['total_usuarios_registrados']; ?></div>
                        <p>Usuarios registrados</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-5">
                <h4 class="text-center mb-4">Top 5 participantes</h4>
                <?php
                $max_likes = !empty($top5) ? max(array_column($top5, 'total_likes')) : 1;
                foreach ($top5 as $participante) {
                    $percentage = ($participante['total_likes'] / $max_likes) * 100;
                    echo '
                    <div class="progress-title">
                        '.htmlspecialchars($participante['username']).' - '.number_format($participante['total_likes']).' likes
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: '.$percentage.'%" 
                            aria-valuenow="'.$participante['total_likes'].'" aria-valuemin="0" aria-valuemax="'.$max_likes.'">
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <h2 class="text-center categoria-titulo">Top 3 Fotógrafos</h2>
        <div class="row">
            <?php if (!empty($ganadores)): ?>
                <?php foreach ($ganadores as $index => $ganador): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card ganador-card h-100">
                            <div class="position-relative">
                                <img src="<?php echo $ganador['foto_perfil'] ? htmlspecialchars($ganador['foto_perfil']) : 'assets/user-default.jpg'; ?>"
                                    alt="<?php echo htmlspecialchars($ganador['username']); ?>" class="card-img-top ganador-img">
                                <?php if ($index == 0): ?>
                                    <div class="medalla medalla-oro">1°</div>
                                <?php elseif ($index == 1): ?>
                                    <div class="medalla medalla-plata">2°</div>
                                <?php else: ?>
                                    <div class="medalla medalla-bronce">3°</div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body text-center">
                                <h3 class="h4"><?php echo htmlspecialchars($ganador['username']); ?></h3>
                                <div class="d-flex justify-content-center gap-4 my-3">
                                    <div>
                                        <div class="fw-bold"><?php echo number_format($ganador['total_likes']); ?></div>
                                        <small class="text-muted">Likes</small>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo $ganador['total_fotos']; ?></div>
                                        <small class="text-muted">Fotos</small>
                                    </div>
                                </div>
                                <a href="perfil.php?id=<?php echo $ganador['user_id']; ?>" class="btn btn-primary btn-sm">
                                    Ver Perfil <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h3>No hay participantes todavía</h3>
                    <p class="mb-4">Sube tus fotos y participa por estos increíbles premios</p>
                    <a href="InicioSesion/inicioSesion.php" class="btn btn-primary">Inicia Sesión ahora</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center categoria-titulo">Cronología del Concurso</h2>
            <div class="row mt-4">
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-4 text-primary mb-3">1</div>
                            <h4>15 Abril 2025</h4>
                            <p>Apertura del concurso</p>
                            <p class="small text-muted">Inicio del periodo para subir fotografías</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-4 text-primary mb-3">2</div>
                            <h4>30 Abril 2025</h4>
                            <p>Primer reporte</p>
                            <p class="small text-muted">Publicación de estadísticas iniciales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-4 text-primary mb-3">3</div>
                            <h4>15 Mayo 2025</h4>
                            <p>Cierre de recepción</p>
                            <p class="small text-muted">Último día para subir fotos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="display-4 text-primary mb-3">4</div>
                            <h4>31 Mayo 2025</h4>
                            <p>Anuncio de ganadores</p>
                            <p class="small text-muted">Fin del concurso y premiación</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img src="assets/logo-white.png" alt="PixFly" style="height: 40px; margin-bottom: 15px;">
                    <p>Plataforma líder en concursos de fotografía digital desde 2010.</p>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope"></i> info@pixfly.com</li>
                        <li><i class="bi bi-phone"></i> +34 123 456 789</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">© 2025 PixFly. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown Timer
        function updateCountdown() {
            const endDate = new Date('May 31, 2025 23:59:59').getTime();
            const now = new Date().getTime();
            const distance = endDate - now;
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
            
            if (distance < 0) {
                clearInterval(countdownTimer);
                document.getElementById('countdown').innerHTML = '<div class="alert alert-success">¡El concurso ha finalizado!</div>';
            }
        }
        
        const countdownTimer = setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</body>
</html>