<?php
require_once 'conexion.php';

header('Content-Type: application/json');

try {
    // Consulta para obtener los likes de todas las imágenes activas
    $sql = "SELECT id, likes FROM imagenes WHERE estado = 'activo'";
    $result = $conn->query($sql);
    
    $likesData = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $likesData[] = [
                'id' => $row['id'],
                'likes' => $row['likes']
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'likes' => $likesData
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los likes: ' . $e->getMessage()
    ]);
}

$conn->close();
?>