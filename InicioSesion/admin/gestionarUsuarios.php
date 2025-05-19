<?php
session_start();
require_once '../../php/conexion.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_admin']) || $_SESSION['user_admin'] != 1) {
    header("Location: https://41183897.servicio-online.net/InicioSesion/inicioSesion.php");
    exit();
}

// Obtener lista de usuarios
$usuarios = [];
$query = "SELECT id, nombre, correo, fecha_registro, admin FROM Usuarios ORDER BY fecha_registro DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

// Procesar acciones (eliminar, cambiar rol, editar, crear)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['eliminar_usuario'])) {
        $id = intval($_POST['id']);
        $query = "DELETE FROM Usuarios WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: gestionarUsuarios.php?success=1");
        exit();
    } elseif (isset($_POST['cambiar_rol'])) {
        $id = intval($_POST['id']);
        $admin = intval($_POST['admin']);
        $query = "UPDATE Usuarios SET admin = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $admin, $id);
        $stmt->execute();
        header("Location: gestionarUsuarios.php?success=2");
        exit();
    } elseif (isset($_POST['editar_usuario'])) {
        $id = intval($_POST['id']);
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $correo = $conn->real_escape_string($_POST['correo']);
        $admin = intval($_POST['admin']);
        
        $query = "UPDATE Usuarios SET nombre = ?, correo = ?, admin = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $nombre, $correo, $admin, $id);
        $stmt->execute();
        header("Location: gestionarUsuarios.php?success=3");
        exit();
    } elseif (isset($_POST['crear_usuario'])) {
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $correo = $conn->real_escape_string($_POST['correo']);
        $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
        $admin = intval($_POST['admin']);
        
        // Verificar si el correo ya existe
        $check_query = "SELECT id FROM Usuarios WHERE correo = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $correo);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            header("Location: gestionarUsuarios.php?error=1");
            exit();
        }
        
        $query = "INSERT INTO Usuarios (nombre, correo, contrasena, admin) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $nombre, $correo, $contrasena, $admin);
        $stmt->execute();
        header("Location: gestionarUsuarios.php?success=4");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios - pixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/stylesIndex.css">
    <style>
        .action-buttons .btn {
            margin-right: 5px;
        }
        .action-buttons .btn:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarUsuarios.php">Gestionar usuarios</i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarImagenes.php">Gestionar imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarIncidencias.php">Gestionar incidencias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestionarReportes.php">Gestionar tickets/reportes</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger" href="../../php/logout.php">
                            Cerrar Sesión <i class="bi bi-box-arrow-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Gestión de Usuarios</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                switch($_GET['success']) {
                    case 1: echo 'Usuario eliminado correctamente'; break;
                    case 2: echo 'Rol de usuario actualizado'; break;
                    case 3: echo 'Usuario actualizado correctamente'; break;
                    case 4: echo 'Usuario creado correctamente'; break;
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                switch($_GET['error']) {
                    case 1: echo 'El correo electrónico ya está registrado'; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Usuarios</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">
                    <i class="bi bi-plus-lg"></i> Nuevo Usuario
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Fecha Registro</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                        <select name="admin" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="0" <?php echo $usuario['admin'] == 0 ? 'selected' : ''; ?>>Usuario</option>
                                            <option value="1" <?php echo $usuario['admin'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                                        </select>
                                        <input type="hidden" name="cambiar_rol" value="1">
                                    </form>
                                </td>
                                <td class="action-buttons">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarUsuarioModal" 
                                            onclick="cargarDatosModal(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nombre'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($usuario['correo'], ENT_QUOTES); ?>', <?php echo $usuario['admin']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                        <button type="submit" name="eliminar_usuario" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear usuario -->
    <div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="gestionarUsuarios.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="crear_usuario" value="1">
                        
                        <div class="mb-3">
                            <label for="nombreNuevo" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombreNuevo" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correoNuevo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correoNuevo" name="correo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contrasenaNuevo" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasenaNuevo" name="contrasena" required minlength="6">
                        </div>
                        
                        <div class="mb-3">
                            <label for="adminNuevo" class="form-label">Rol</label>
                            <select class="form-select" id="adminNuevo" name="admin" required>
                                <option value="0">Usuario</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="gestionarUsuarios.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="usuarioId">
                        <input type="hidden" name="editar_usuario" value="1">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin" class="form-label">Rol</label>
                            <select class="form-select" id="admin" name="admin" required>
                                <option value="0">Usuario</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../../php/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarDatosModal(id, nombre, correo, admin) {
            document.getElementById('usuarioId').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('correo').value = correo;
            document.getElementById('admin').value = admin;
        }
    </script>
</body>
</html>