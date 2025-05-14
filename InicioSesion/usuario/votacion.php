<?php
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }
    </style>
</head>
<body>

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
