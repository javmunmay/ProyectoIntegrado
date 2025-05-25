<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - pixFly</title>
    <link rel="icon" type="image/png" href="../assets/logoIcon.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

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
            color: #090643;
            font-weight: bold;
        }

        .form-container .btn-primary {
            background-color: #090643;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
            width: 100%;
        }

        .form-container .btn-primary:hover {
            background-color:rgb(16, 11, 112);
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
            color: #090643;
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

        label a{
            color: #090643;
        }
    </style>
</head>

<body>

    <?php include '../php/nav.php'; ?>

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



    <?php include '../php/footer.php'; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>