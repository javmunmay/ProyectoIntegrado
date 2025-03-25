CREATE TABLE imagenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_archivo VARCHAR(255) NOT NULL,
    ruta VARCHAR(512) NOT NULL,
    titulo VARCHAR(100),
    descripcion TEXT,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    likes INT DEFAULT 0,
    dislikes INT DEFAULT 0,
    estado ENUM('activo', 'inactivo', 'pendiente', 'eliminado') DEFAULT 'activo',
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);