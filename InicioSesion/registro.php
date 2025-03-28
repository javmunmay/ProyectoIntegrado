<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Derrapix</title>
    <link rel="icon" type="image/png" href="../assets/logoIcon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        
        .main-content {
            flex: 1;
            /* Ocupa el espacio restante */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: #2a3d74 !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #2a3d74 !important;
            font-weight: bold;
        }

        .nav-link:hover {
            color: #1E3A5F !important;
        }

        .form-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2a3d74;
            font-weight: bold;
        }

        .form-container .btn-primary {
            background-color: #2a3d74;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
            width: 100%;
        }

        .form-container .btn-primary:hover {
            background-color: #1E3A5F;
        }

        .form-container .form-control {
            margin-bottom: 15px;
        }

        .form-container .form-control:focus {
            border-color: #2a3d74;
            box-shadow: 0 0 0 0.2rem rgba(42, 61, 116, 0.25);
        }

        .form-container .text-center {
            margin-top: 15px;
        }

        .form-container .text-center a {
            color: #2a3d74;
            text-decoration: none;
            font-weight: bold;
        }

        .form-container .text-center a:hover {
            text-decoration: underline;
        }

       
        .footer {
            background-color: #1E3A5F;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
            /* Empuja el footer hacia abajo */
        }
    </style>
</head>

<body>


    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
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
                        <a class="btn btn-primary" href="registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-primary" href="inicioSesion.php">
                            Iniciar Sesión <i class="bi bi-key"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Formulario de Registro -->
        <div class="form-container">
            <h2>Regístrarse</h2>
            <form action="../php/registro.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="correo" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="contrasena" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirmar_contrasena"
                        required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="politica" name="politica" required>
                    <label class="form-check-label" for="politica">
                        Acepto la <a href="politica-privacidad.php" target="_blank">Política de Privacidad</a>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">Registrarse</button>
            </form>
            <div class="text-center mt-3">
                <p>¿Ya tienes una cuenta? <a href="inicioSesion.php">Inicia Sesión</a></p>
            </div>
        </div>
    </div>


    <footer class="footer text-center">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Rally Fotográfico. Todos los derechos reservados.</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>