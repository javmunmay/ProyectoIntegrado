<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - pixFly</title>
    <link rel="icon" type="image/png" href="../assets/logoIcon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Contenido principal */
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

        .nav-link:hover {
            color: #1E3A5F !important;
        }

        /* Estilos para el formulario de inicio de sesión */
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            color: #090643;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-control {
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #2a3d74;
            box-shadow: 0 0 5px rgba(42, 61, 116, 0.5);
        }

        .btn-login {
            background-color: #090643;
            color: white;
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color: #1E3A5F;
            color: white;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            color: #090643;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Estilos para el footer */
        .footer {
            background-color: #1E3A5F;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: auto;
            /* Empuja el footer hacia abajo */
        }

        .btn-registrarse{
            background-color: #090643;
            color: white;
            padding: 7px;
        }

        .btn-registrarse:hover{
            background-color:rgb(12, 8, 89);
            color: white;
        }

        .btn-iniciosesion{
            background-color: white;
            border: solid 1px #090643;
            color: #090643;
            padding: 7px;
        }

        .btn-iniciosesion:hover{
            background-color: #090643;
            color: white;
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
                        <a class="nav-link" href="/">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../ganadores.php">Ganadores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-registrarse" href="registro.php">Registrarse</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2">
                        <a class="btn btn-iniciosesion" href="inicioSesion.php">
                            Iniciar Sesión <i class="bi bi-box-arrow-in-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="main-content">

        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <form action="../php/login.php" method="POST">

                <!-- Mostrar mensajes de error -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?php
                        switch ($_GET['error']) {
                            case 1:
                                echo "Por favor completa todos los campos";
                                break;
                            case 2:
                                echo "Error del sistema. Intenta más tarde";
                                break;
                            case 3:
                                echo "Correo o contraseña incorrectos";
                                break;
                            default:
                                echo "Error desconocido";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="correo" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo"
                        placeholder="Ingresa tu correo electrónico" required>
                </div>

                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasena" name="contrasena"
                        placeholder="Ingresa tu contraseña" required>
                </div>

                <button type="submit" class="btn btn-login">Iniciar Sesión</button>
            </form>

            <div class="register-link">
                ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </div>
    </div>


    <?php include '../php/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>