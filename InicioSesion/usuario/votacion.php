<?php
<<<<<<< HEAD
session_start();
require_once '../../php/conexion.php';
require_once '../../php/funciones.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../InicioSesion/inicioSesion.php");
    exit();
}

$usuario_id = $_SESSION['user_id'];

// Consulta para obtener imágenes activas con info de voto
$sql_imagenes = "SELECT 
                i.id AS imagen_id,
                i.ruta,
                i.titulo,
                i.descripcion,
                i.likes,
                u.id AS usuario_id,
                u.nombre AS usuario_nombre,
                u.foto_perfil,
                EXISTS(SELECT 1 FROM votos WHERE usuario_id = ? AND imagen_id = i.id) AS ya_votado
            FROM 
                imagenes i
            JOIN 
                Usuarios u ON i.usuario_id = u.id
            WHERE 
                i.estado = 'activo'
            ORDER BY 
                i.likes DESC";

$stmt_imagenes = $conn->prepare($sql_imagenes);
$stmt_imagenes->bind_param("i", $usuario_id);
$stmt_imagenes->execute();
$result_imagenes = $stmt_imagenes->get_result();
$imagenes = $result_imagenes->fetch_all(MYSQLI_ASSOC);
$stmt_imagenes->close();

// Endpoint para AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax']) && isset($_POST['imagen_id'])) {
    header('Content-Type: application/json');
    $imagen_id = intval($_POST['imagen_id']);
    $response = ['success' => false];
    
    $conn->begin_transaction();
    try {
        // Verificar si ya votó
        $sql_verificar = "SELECT id FROM votos WHERE usuario_id = ? AND imagen_id = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("ii", $usuario_id, $imagen_id);
        $stmt_verificar->execute();
        $ya_votado = $stmt_verificar->get_result()->num_rows > 0;
        $stmt_verificar->close();
        
        if ($ya_votado) {
            // Quitar el voto
            $sql_quitar = "DELETE FROM votos WHERE usuario_id = ? AND imagen_id = ?";
            $stmt_quitar = $conn->prepare($sql_quitar);
            $stmt_quitar->bind_param("ii", $usuario_id, $imagen_id);
            $stmt_quitar->execute();
            $stmt_quitar->close();
            
            $sql_actualizar = "UPDATE imagenes SET likes = likes - 1 WHERE id = ?";
            $nuevo_estado = false;
        } else {
            // Añadir voto
            $sql_añadir = "INSERT INTO votos (usuario_id, imagen_id) VALUES (?, ?)";
            $stmt_añadir = $conn->prepare($sql_añadir);
            $stmt_añadir->bind_param("ii", $usuario_id, $imagen_id);
            $stmt_añadir->execute();
            $stmt_añadir->close();
            
            $sql_actualizar = "UPDATE imagenes SET likes = likes + 1 WHERE id = ?";
            $nuevo_estado = true;
        }
        
        // Actualizar contador
        $stmt_actualizar = $conn->prepare($sql_actualizar);
        $stmt_actualizar->bind_param("i", $imagen_id);
        $stmt_actualizar->execute();
        
        // Obtener nuevo conteo
        $sql_count = "SELECT likes FROM imagenes WHERE id = ?";
        $stmt_count = $conn->prepare($sql_count);
        $stmt_count->bind_param("i", $imagen_id);
        $stmt_count->execute();
        $result = $stmt_count->get_result();
        $likes = $result->fetch_assoc()['likes'];
        
        $conn->commit();
        
        $response = [
            'success' => true,
            'likes' => $likes,
            'nuevo_estado' => $nuevo_estado
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}
=======
// Iniciar la sesión para poder trabajar con los usuarios
session_start();

// Incluir el archivo de conexión a la base de datos
require_once '../../php/conexion.php'; // Asegúrate de que la ruta sea correcta

// Asegurarse de que el ID de la imagen es pasado por GET
if (!isset($_GET['imagen_id']) || !is_numeric($_GET['imagen_id'])) {
    die("Imagen no válida.");
}

$imagen_id = $_GET['imagen_id'];

// Obtener la imagen desde la base de datos
$sql = "SELECT * FROM imagenes WHERE id = $imagen_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Imagen no encontrada.");
}

$imagen = $result->fetch_assoc();

// Comprobar si el usuario ya ha votado
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

if ($usuario_id) {
    $sql_check_vote = "SELECT * FROM votos WHERE usuario_id = $usuario_id AND imagen_id = $imagen_id";
    $check_vote_result = $conn->query($sql_check_vote);

    // Si ya votó, no permitimos votar de nuevo (descomenta esto si deseas bloquear votos duplicados)
    // if ($check_vote_result->num_rows > 0) {
    //     die("Ya has votado por esta imagen.");
    // }
}

// Procesar el voto (positivo o negativo)
if (isset($_POST['vote'])) {
    $vote_type = $_POST['vote']; // "like" o "dislike"

    if ($vote_type === 'like') {
        $sql_update = "UPDATE imagenes SET likes = likes + 1 WHERE id = $imagen_id";
    } elseif ($vote_type === 'dislike') {
        $sql_update = "UPDATE imagenes SET dislikes = dislikes + 1 WHERE id = $imagen_id";
    } else {
        die("Voto no válido.");
    }

    if ($conn->query($sql_update) === TRUE) {
        // Guardar el voto en la tabla de votos
        if ($usuario_id) {
            $sql_insert_vote = "INSERT INTO votos (usuario_id, imagen_id, voto) VALUES ($usuario_id, $imagen_id, '$vote_type')";
            $conn->query($sql_insert_vote);
        }

        // Redirigir para evitar reenvío de formulario
        header("Location: votacion.php?imagen_id=$imagen_id");
        exit();
    } else {
        echo "Error al actualizar el voto: " . $conn->error;
    }
}

// Mostrar los votos de la imagen
$likes = $imagen['likes'];
$dislikes = $imagen['dislikes'];

// Cerrar la conexión a la base de datos
$conn->close();
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Votación | PixFly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/stylesIndex.css">
    <link rel="icon" type="image/png" href="assets/logoIcon.png">
    <style>
        .hero-section {
            background: linear-gradient(rgba(9, 6, 67, 0.9), rgba(9, 6, 67, 0.9)), 
                        url('assets/foto-votacion.jpg') center/cover;
            color: white;
            padding: 100px 0;
        }
        
        .card-imagen {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            border: none;
        }
        
        .card-imagen:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .img-container {
            height: 250px;
            overflow: hidden;
        }
        
        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .card-imagen:hover .img-container img {
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
        
        .btn-votar {
            background-color: #090643;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 20px;
            transition: all 0.3s;
        }
        
        .btn-votar:hover {
            background-color: #120d6b;
            transform: translateY(-2px);
        }
        
        .btn-votado {
            background-color: #28a745;
        }
        
        .btn-votado:hover {
            background-color: #218838;
        }
        
        .badge-likes {
            font-size: 0.9rem;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        
        .section-title:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #090643, #4e73df);
        }

        .btn-outline-danger {
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btnSubir{
            background-color: #090643;
            padding-left: 25px;
            padding-right: 25px;
            padding-top: 12px;
            padding-bottom: 12px;
            color: white;
            border-radius: 6px;
            font-size: 19px;
            text-decoration: none;
=======
    <title>Votación Concurso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
        }
        .imagen-container {
            margin: 20px auto;
            max-width: 600px;
        }
        .imagen {
            width: 100%;
            max-width: 500px;
        }
        .botones-voto {
            margin-top: 10px;
        }
        .botones-voto button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
        }
        .botones-voto .like {
            background-color: #4CAF50;
            color: white;
        }
        .botones-voto .dislike {
            background-color: #f44336;
            color: white;
        }
        .votos {
            margin-top: 20px;
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
        }
    </style>
</head>
<body>
<<<<<<< HEAD
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <img src="../../assets/logo.png" alt="Logo Rally Fotográfico" class="logo" style="height: 50px;">
            </a>
            <a href="../../php/subir_imagen.php" class="btnSubir ms-auto me-3">
                <i class="bi bi-cloud-arrow-up"></i> Subir
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="miPerfil.php">Mi Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="misImagenes.php">Mis Imágenes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="votacion.php">Votación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contacto.php">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-danger" href="../../php/logout.php">
                            Cerrar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Vota por las mejores fotografías</h1>
            <p class="lead mb-5">Tu voto ayuda a reconocer el talento de nuestra comunidad</p>
            <a href="#galeria" class="btn btn-light btn-lg px-4">Ver participantes</a>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5" id="galeria">
        <div class="container">
            <h2 class="text-center section-title">Fotografías en concurso</h2>
            
            <div class="row g-4">
                <?php if (!empty($imagenes)): ?>
                    <?php foreach ($imagenes as $imagen): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card card-imagen h-100">
                                <div class="img-container">
                                    <img src="/../../<?php echo htmlspecialchars($imagen['ruta']); ?>" 
                                         alt="<?php echo htmlspecialchars($imagen['titulo']); ?>">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($imagen['titulo']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($imagen['descripcion']); ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="user-info">
                                            <img src="/../../assets/<?php echo htmlspecialchars($imagen['foto_perfil'] ?? 'user-default.jpg'); ?>" 
                                                 alt="<?php echo htmlspecialchars($imagen['usuario_nombre']); ?>" 
                                                 class="user-avatar">
                                            <span><?php echo htmlspecialchars($imagen['usuario_nombre']); ?></span>
                                        </div>
                                        
                                        <button type="button" 
                                                class="btn-votar <?php echo $imagen['ya_votado'] ? 'btn-votado' : ''; ?> votar-btn"
                                                data-imagen-id="<?php echo $imagen['imagen_id']; ?>">
                                            <i class="bi <?php echo $imagen['ya_votado'] ? 'bi-check-circle-fill' : 'bi-heart-fill'; ?> me-1"></i>
                                            <span class="badge badge-likes ms-1 likes-count"><?php echo $imagen['likes']; ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card py-5 text-center">
                            <h3 class="text-muted">No hay fotografías en concurso actualmente</h3>
                            <p>Visita más tarde para participar en la votación</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center section-title">Cómo funciona la votación</h2>
            
            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary text-white rounded-circle mx-auto mb-3" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-check-circle fs-4"></i>
                            </div>
                            <h4>Un voto por imagen</h4>
                            <p class="text-muted">Puedes votar por cada fotografía una sola vez.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary text-white rounded-circle mx-auto mb-3" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-graph-up fs-4"></i>
                            </div>
                            <h4>Las más votadas ganan</h4>
                            <p class="text-muted">Las fotografías con más votos al final del periodo ganarán premios.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary text-white rounded-circle mx-auto mb-3" 
                                 style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                            <h4>Fechas importantes</h4>
                            <p class="text-muted">La votación cierra el último día del mes. ¡No te quedes fuera!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../../php/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.votar-btn').click(function() {
            const btn = $(this);
            const imagen_id = btn.data('imagen-id');
            
            // Deshabilitar botón temporalmente para evitar múltiples clicks
            btn.prop('disabled', true);
            
            $.ajax({
                url: 'votacion.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    ajax: true,
                    imagen_id: imagen_id
                },
                success: function(response) {
                    if (response.success) {
                        // Actualizar contador
                        btn.find('.likes-count').text(response.likes);
                        
                        // Cambiar estado visual
                        if (response.nuevo_estado) {
                            btn.addClass('btn-votado');
                            btn.find('i').removeClass('bi-heart-fill').addClass('bi-check-circle-fill');
                        } else {
                            btn.removeClass('btn-votado');
                            btn.find('i').removeClass('bi-check-circle-fill').addClass('bi-heart-fill');
                        }
                    } else {
                        console.error('Error:', response.error || 'Error desconocido');
                        alert('Ocurrió un error al procesar tu voto');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Error de conexión. Intenta nuevamente.');
                },
                complete: function() {
                    // Rehabilitar botón después de la respuesta
                    btn.prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>
=======

    <div class="imagen-container">
        <h2>Vota por esta imagen</h2>
        <img src="/../../<?php echo $imagen['ruta_imagen']; ?>" alt="Imagen del concurso" class="imagen">
        <div class="botones-voto">
            <form method="post" action="">
                <button type="submit" name="vote" value="like" class="like">Me gusta</button>
                <button type="submit" name="vote" value="dislike" class="dislike">No me gusta</button>
            </form>
        </div>

        <div class="votos">
            <p>Me gusta: <?php echo $likes; ?> | No me gusta: <?php echo $dislikes; ?></p>
        </div>
    </div>

</body>
</html>
>>>>>>> 97d5d9017f521a3eb44cb8284144212f6cac5a52
