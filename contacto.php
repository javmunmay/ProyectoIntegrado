<?php

// Configurar cabeceras para evitar el almacenamiento en caché
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contacto | Rally Fotográfico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/logo-rally.png" />
    <link rel="stylesheet" type="text/css" href="css/stylesContacto.css">

</head>

<body>
    <header>
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
    </header>

    <main class="container-contacto">
        <section class="hero">
            <h1>Contáctanos</h1>
            <p>
                ¿Tienes preguntas, sugerencias o problemas técnicos? Estamos aquí para ayudarte en tu experiencia con el Rally Fotográfico.
            </p>
        </section>

        <!-- Formulario de contacto -->
        <section class="contact-form">
            <h2>Formulario de Contacto</h2>
            <form action="../php/procesar_incidencia.php" method="POST">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['user_id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" 
                           value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>" 
                           <?php echo isset($_SESSION['user_id']) ? 'readonly' : 'required'; ?> />
                </div>
                
                <div class="form-group">
                    <label for="correo">Correo electrónico:</label>
                    <input type="email" id="correo" name="correo" 
                           value="<?php echo isset($_SESSION['correo']) ? $_SESSION['correo'] : ''; ?>" 
                           <?php echo isset($_SESSION['user_id']) ? 'readonly' : 'required'; ?> />
                </div>
                
                <div class="form-group">
                    <label for="titulo">Asunto:</label>
                    <select id="titulo" name="titulo" required>
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
                    <textarea id="descripcion" name="descripcion" rows="5" 
                              placeholder="Describe tu consulta o problema con detalle..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="prioridad">Prioridad:</label>
                    <select id="prioridad" name="prioridad">
                        <option value="media" selected>Media (respuesta en 24-48h)</option>
                        <option value="alta">Alta (problema urgente)</option>
                        <option value="baja">Baja (consulta general)</option>
                    </select>
                </div>
                
                <?php if(!isset($_SESSION['user_id'])): ?>
                <div class="form-group">
                    <input type="checkbox" id="politica" name="politica" required />
                    <label for="politica">Acepto la <a href="../politica-privacidad.php" target="_blank">política de privacidad</a></label>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="btn-submit">Enviar mensaje</button>
            </form>
        </section>

        <!-- Información de contacto -->
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

        <!-- Redes sociales -->
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
    <script src="../js/main.js"></script>
    
    <script>
    // Manejo de mensajes de retroalimentación
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const mensaje = urlParams.get('mensaje');

        if (mensaje === 'incidencia_registrada') {
            mostrarMensaje('Gracias por contactarnos. Hemos registrado tu incidencia y te responderemos pronto.', 'success');
        } else if (mensaje === 'error_envio') {
            mostrarMensaje('Hubo un error al enviar tu mensaje. Por favor inténtalo de nuevo.', 'error');
        }

        function mostrarMensaje(texto, tipo) {
            const mensajeDiv = document.createElement('div');
            mensajeDiv.textContent = texto;
            mensajeDiv.className = `mensaje-${tipo}`;
            
            const formContainer = document.querySelector('.contact-form');
            formContainer.insertBefore(mensajeDiv, formContainer.firstChild);
            
            setTimeout(() => {
                mensajeDiv.style.opacity = '0';
                setTimeout(() => mensajeDiv.remove(), 500);
            }, 5000);
        }
    };
    </script>
</body>
</html>