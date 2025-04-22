<?php
session_start();
require_once 'php/conexion.php';
/*
// Consulta para obtener los usuarios con más likes en sus imágenes
$sql = "SELECT 
            u.id AS user_id,
            u.username,
            u.foto_perfil,
            SUM(f.likes) AS total_likes,
            COUNT(f.id) AS total_fotos
        FROM 
            usuarios u
        LEFT JOIN 
            fotos f ON u.id = f.usuario_id
        GROUP BY 
            u.id
        HAVING 
            total_likes > 0
        ORDER BY 
            total_likes DESC
        LIMIT 12";

$result = $conn->query($sql);
$talentos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $talentos[] = $row;
    }
}*/
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votaciones - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="css/stylesNuevosTalentos.css">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
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
                        <a class="nav-link active" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ganadores.php">Ganadores</a>
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


    <section class="hero-section" data-aos="fade">
        <div class="container">
            <h1 data-aos="fade-up">Vota por la foto que más te guste</h1>
            <p data-aos="fade-up" data-aos-delay="200">Los fotógrafos emergentes que están revolucionando el mundo del
                rally con sus imágenes</p>
        </div>
    </section>


    <section class="talentos-container">
        <div class="container">
            <div class="row">
                <?php if (!empty($talentos)): ?>
                    <?php foreach ($talentos as $talento): ?>
                        <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="<?php echo rand(100, 300); ?>">
                            <div class="talento-card">
                                <img src="<?php echo $talento['foto_perfil'] ? htmlspecialchars($talento['foto_perfil']) : 'assets/user-default.jpg'; ?>"
                                    alt="<?php echo htmlspecialchars($talento['username']); ?>" class="talento-img">
                                <h3 class="talento-name"><?php echo htmlspecialchars($talento['username']); ?></h3>
                                <div class="talento-stats">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $talento['total_likes']; ?></div>
                                        <div class="stat-label">Likes</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $talento['total_fotos']; ?></div>
                                        <div class="stat-label">Fotos</div>
                                    </div>
                                </div>
                                <a href="perfil.php?id=<?php echo $talento['user_id']; ?>" class="btn btn-view-profile">Ver
                                    Perfil</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h3>No hay talentos para mostrar todavía</h3>
                        <p>Sé el primero en subir tus fotos y aparecer aquí</p>
                        <a href="InicioSesion/inicioSesion.php" class="btn btn-primary">Inicia Sesión ahora</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <?php include 'php/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>