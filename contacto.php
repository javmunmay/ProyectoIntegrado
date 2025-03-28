<?php
session_start();
require_once 'php/conexion.php';

// Configurar cabeceras para evitar caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Manejo de mensajes de retroalimentación
$mensaje = '';
$tipoMensaje = '';

if (isset($_GET['success'])) {
    $mensaje = 'Gracias por contactarnos. Hemos recibido tu mensaje correctamente.';
    $tipoMensaje = 'success';
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'campos_obligatorios':
            $mensaje = 'Por favor completa todos los campos obligatorios.';
            break;
        case 'politica_requerida':
            $mensaje = 'Debes aceptar la política de privacidad.';
            break;
        case 'error_bd':
            $mensaje = 'Ocurrió un error al enviar tu mensaje. Por favor inténtalo de nuevo.';
            break;
        default:
            $mensaje = 'Ocurrió un error inesperado.';
    }
    $tipoMensaje = 'danger';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Rally Fotográfico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
    <link rel="stylesheet" type="text/css" href="css/stylesContacto.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trending.php">Trending <i class="bi bi-graph-up"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nuevosTalentos.php">Nuevos Talentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ganadoras">Top Shots</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary" href="InicioSesion/registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary" href="InicioSesion/inicioSesion.php">
                            Iniciar Sesión <i class="bi bi-key"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-contacto">
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipoMensaje === 'success' ? 'success' : 'danger' ?>">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <section class="hero">
            <h1>Contáctanos</h1>
            <p>¿Tienes preguntas, sugerencias o problemas técnicos? Estamos aquí para ayudarte.</p>
        </section>

        <section class="contact-form">
            <h2>Formulario de Contacto</h2>
            <form action="php/procesar_incidencia.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="titulo">Asunto:</label>
                    <select id="titulo" name="titulo" class="form-control" required>
                        <option value="" disabled selected>Selecciona un asunto</option>
                        <option value="problema_subida">Problema al subir fotos</option>
                        <option value="problema_desafio">Problema con un desafío</option>
                        <option value="problema_cuenta">Problema con mi cuenta</option>
                        <option value="derechos_autor">Consulta sobre derechos de autor</option>
                        <option value="sugerencia">Sugerencia para mejorar</option>
                        <option value="otro">Otro asunto</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción detallada:</label>
                    <textarea id="descripcion" name="descripcion" rows="5" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="prioridad">Prioridad:</label>
                    <select id="prioridad" name="prioridad" class="form-control">
                        <option value="media" selected>Media (respuesta en 24-48h)</option>
                        <option value="alta">Alta (problema urgente)</option>
                        <option value="baja">Baja (consulta general)</option>
                    </select>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" id="politica" name="politica" class="form-check-input" required>
                    <label for="politica" class="form-check-label">Acepto la <a href="politica-privacidad.php" target="_blank">política de privacidad</a></label>
                </div>

                <button type="submit" class="btn btn-primary">Enviar mensaje</button>
            </form>
        </section>


        <section class="info-contacto">
            <h2>Otras formas de contacto</h2>
            <div class="contacto-item">
                <i class="fas fa-envelope"></i>
                <p>contacto@rallyfotografico.com</p>
            </div>
            <div class="contacto-item">
                <i class="fas fa-phone"></i>
                <p>+34 123 456 789</p>
            </div>
            <div class="contacto-item">
                <i class="fas fa-map-marker-alt"></i>
                <p>Calle Fotografía, 123, 28001 Madrid</p>
            </div>
        </section>


        <section class="redes-sociales">
            <h2>Síguenos en redes</h2>
            <div class="social-icons">
                <a href="#" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </section>
    </main>

    <?php include 'php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>