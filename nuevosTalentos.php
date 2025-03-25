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
    <title>Nuevos Talentos - Rally Fotográfico</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" type="text/css" href="css/stylesNuevosTalentos.css">

</head>

<body>
    <!-- Menú de navegación -->
    <nav>
        <a href="/">
            <img src="assets/logo.png" alt="Logo Rally Fotográfico" class="logo" />
        </a>
        <button class="menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Inicio</a></li>
            <li><a href="trending.php">Trending 📈</a></li>
            <li><a href="nuevosTalentos.php">Nuevos Talentos</a></li>
            <li><a href="#ganadoras">Top Shots</a></li>
            <li><a href="contacto.php">Contacto</a></li>
            <li><a href="InicioSesion/registro.php" class="login-btn">Registrarse</a></li>
            <li><a href="InicioSesion/inicioSesion.php" class="login-btn">Iniciar Sesión 🔑</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" data-aos="fade">
        <div class="container">
            <h1 data-aos="fade-up">Descubre los Nuevos Talentos</h1>
            <p data-aos="fade-up" data-aos-delay="200">Los fotógrafos emergentes que están revolucionando el mundo del
                rally con sus imágenes</p>
        </div>
    </section>

    <!-- Sección de Talentos -->
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

    <!-- Pie de página -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Rally Fotográfico. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS (Animate On Scroll) -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
</body>

</html>