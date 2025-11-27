-- Tabla para almacenar tokens de recuperación de contraseña
-- Sistema de Gestión de Reciclaje

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL COMMENT 'ID del usuario',
  `token` varchar(255) NOT NULL COMMENT 'Token único para restablecer contraseña',
  `email` varchar(150) NOT NULL COMMENT 'Email del usuario (para verificación)',
  `expira_en` datetime NOT NULL COMMENT 'Fecha y hora de expiración del token',
  `usado` tinyint(1) DEFAULT 0 COMMENT 'Indica si el token ya fue usado',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_email` (`email`),
  KEY `idx_expira` (`expira_en`),
  KEY `idx_usado` (`usado`),
  CONSTRAINT `password_reset_tokens_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tokens para recuperación de contraseña';

