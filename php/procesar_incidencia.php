<?php
require_once 'conexion.php';

// Verificar método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../contacto.php?error=metodo_no_valido");
    exit();
}

// Validar campos obligatorios
$camposRequeridos = ['nombre', 'correo', 'titulo', 'descripcion'];
$camposFaltantes = [];

foreach ($camposRequeridos as $campo) {
    if (empty(trim($_POST[$campo]))) {
        $camposFaltantes[] = $campo;
    }
}

if (!empty($camposFaltantes)) {
    header("Location: ../contacto.php?error=campos_obligatorios");
    exit();
}

// Verificar política de privacidad
if (!isset($_POST['politica'])) {
    header("Location: ../contacto.php?error=politica_requerida");
    exit();
}

// Obtener y sanitizar datos
$nombre = $conn->real_escape_string(trim($_POST['nombre']));
$correo = $conn->real_escape_string(trim($_POST['correo']));
$titulo = $conn->real_escape_string(trim($_POST['titulo']));
$descripcion = $conn->real_escape_string(trim($_POST['descripcion']));
$prioridad = isset($_POST['prioridad']) ? $conn->real_escape_string($_POST['prioridad']) : 'media';
$politicaAceptada = 1; // Porque ya validamos que está marcado

// Validar prioridad
$prioridadesValidas = ['baja', 'media', 'alta'];
if (!in_array($prioridad, $prioridadesValidas)) {
    $prioridad = 'media';
}

// Insertar en la base de datos
try {
    $query = "INSERT INTO incidencias (
                nombre_contacto, 
                correo_contacto, 
                titulo, 
                descripcion, 
                prioridad, 
                politica_aceptada,
                estado
              ) VALUES (?, ?, ?, ?, ?, ?, 'pendiente')";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $nombre, $correo, $titulo, $descripcion, $prioridad, $politicaAceptada);
    
    if ($stmt->execute()) {
        header("Location: ../contacto.php?success=true");
        exit();
    } else {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Error en procesar_incidencia: " . $e->getMessage());
    header("Location: ../contacto.php?error=error_bd");
    exit();
} finally {
    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>