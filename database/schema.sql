--
-- Estructura de la tabla `roles`
--
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `can_manage_users` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_manage_roles` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_manage_sections` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_manage_inventory` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_view_reports` BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar roles básicos
INSERT IGNORE INTO `roles` (`id`, `nombre`, `can_manage_users`, `can_manage_roles`, `can_manage_sections`, `can_manage_inventory`, `can_view_reports`) VALUES
(1, 'Administrador', TRUE, TRUE, TRUE, TRUE, TRUE),
(2, 'Vendedor', FALSE, FALSE, FALSE, TRUE, FALSE),
(3, 'Cliente', FALSE, FALSE, FALSE, FALSE, FALSE);

--
-- Estructura de la tabla `usuarios`
--
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre_usuario` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol_id` INT NOT NULL,
    `activo` BOOLEAN NOT NULL DEFAULT TRUE, -- Para soft delete
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario admin por defecto (admin / root2514)
-- La contraseña 'root2514' debe ser hasheada con password_hash('root2514', PASSWORD_DEFAULT) en PHP
INSERT IGNORE INTO `usuarios` (`id`, `nombre_usuario`, `email`, `password_hash`, `rol_id`, `activo`) VALUES
(1, 'admin', 'admin@sistema.com', '$2y$12$Q/O1TvLOAfTCRptJC68U1uV74/awhLjb9jEnKFnPLA2iXrnj9SEpa', 1, TRUE); -- Hash de 'root2514'

--
-- Estructura de la tabla `secciones`
--
CREATE TABLE IF NOT EXISTS `secciones` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL UNIQUE,
    `descripcion` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de la tabla `partes` (Inventario)
--
CREATE TABLE IF NOT EXISTS `partes` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` TEXT,
    `tipo_parte` VARCHAR(100), -- Ej. Puerta, Motor, Faro
    `marca_auto` VARCHAR(100),
    `modelo_auto` VARCHAR(100),
    `anio_auto` INT,
    `precio` DECIMAL(10, 2) NOT NULL,
    `cantidad_disponible` INT NOT NULL DEFAULT 0,
    `imagen_url` VARCHAR(255), -- Ruta a la imagen grande
    `thumbnail_url` VARCHAR(255), -- Ruta al thumbnail
    `seccion_id` INT NOT NULL,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de la tabla `comentarios`
--
CREATE TABLE IF NOT EXISTS `comentarios` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `parte_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `parent_id` INT NULL DEFAULT NULL,
    `texto_comentario` TEXT NOT NULL,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`parte_id`) REFERENCES `partes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `comentarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Estructura de la tabla `vendido_parte` (Para partes vendidas)
--
CREATE TABLE IF NOT EXISTS `vendido_parte` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `parte_original_id` INT NOT NULL, -- Referencia a la parte original (si se mantiene un registro o se elimina de `partes`)
    `nombre_parte` VARCHAR(255) NOT NULL,
    `precio_venta` DECIMAL(10, 2) NOT NULL,
    `usuario_vendedor_id` INT NOT NULL,
    `fecha_venta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`usuario_vendedor_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
    -- No hay FK a `partes` si la parte se elimina de la tabla `partes` al venderse.
    -- Si `partes` usa cantidad_disponible y se mantiene el registro, entonces sí habría FK.
    -- La GuíaProyecto dice "mover registro a tabla VENDIDO_PARTE", lo que implica que el registro ya no estará en `partes`.
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
