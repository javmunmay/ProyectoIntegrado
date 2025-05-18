<?php
// Obtener datos del usuario
function obtenerDatosUsuario($conn, $usuario_id) {
    $sql = "SELECT *, 
            (SELECT COUNT(*) FROM imagenes WHERE usuario_id = ?) as total_imagenes,
            (SELECT SUM(likes) FROM imagenes WHERE usuario_id = ?) as total_likes,
            (SELECT COUNT(*) FROM imagenes WHERE usuario_id = ?) * 10 as espacio_utilizado
            FROM Usuarios WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $usuario_id, $usuario_id, $usuario_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Contar imágenes del usuario
function contarImagenesUsuario($conn, $usuario_id) {
    $sql = "SELECT COUNT(*) as total FROM imagenes WHERE usuario_id = ? AND estado IN ('activo', 'pendiente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row['total'];
}

// Obtener imágenes recientes del usuario
function obtenerImagenesRecientes($conn, $usuario_id, $limite = 4) {
    $sql = "SELECT id, ruta, titulo FROM imagenes WHERE usuario_id = ? AND estado = 'activo' ORDER BY fecha_subida DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $limite);
    $stmt->execute();
    $result = $stmt->get_result();
    $imagenes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $imagenes;
}

// Obtener estadísticas del usuario
function obtenerEstadisticasUsuario($conn, $usuario_id) {
    $sql = "SELECT 
            (SELECT COUNT(*) FROM imagenes WHERE usuario_id = ?) as total_imagenes,
            (SELECT SUM(likes) FROM imagenes WHERE usuario_id = ?) as total_likes,
            (SELECT COUNT(*) FROM imagenes WHERE usuario_id = ?) * 10 as espacio_utilizado";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $usuario_id, $usuario_id, $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Actualizar perfil del usuario
function actualizarPerfil($conn, $usuario_id, $nombre, $ubicacion, $biografia, $foto_perfil = null) {
    if ($foto_perfil) {
        $sql = "UPDATE Usuarios SET nombre = ?, ubicacion = ?, biografia = ?, foto_perfil = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $ubicacion, $biografia, $foto_perfil, $usuario_id);
    } else {
        $sql = "UPDATE Usuarios SET nombre = ?, ubicacion = ?, biografia = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $ubicacion, $biografia, $usuario_id);
    }
    
    return $stmt->execute();
}
?>