CREATE TABLE `bases_concurso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha_inicio_concurso` date NOT NULL COMMENT 'Fecha de inicio del concurso',
  `fecha_fin_concurso` date NOT NULL COMMENT 'Fecha de finalización del concurso',
  `max_imagenes_por_usuario` int NOT NULL DEFAULT 10 COMMENT 'Número máximo de imágenes que puede subir cada usuario',
  `extensiones_permitidas` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'jpg,jpeg,png' COMMENT 'Extensiones de archivo permitidas separadas por comas',
  `tamano_maximo_mb` int NOT NULL DEFAULT 5 COMMENT 'Tamaño máximo en MB por imagen',
  `votos_por_usuario` int NOT NULL DEFAULT 3 COMMENT 'Número de votos permitidos por usuario',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `usuario_admin_id` int DEFAULT NULL COMMENT 'ID del administrador que configuró estas bases',
  PRIMARY KEY (`id`),
  KEY `usuario_admin_id` (`usuario_admin_id`),
  CONSTRAINT `bases_concurso_ibfk_1` FOREIGN KEY (`usuario_admin_id`) REFERENCES `Usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla que almacena las bases y reglas del concurso fotográfico';