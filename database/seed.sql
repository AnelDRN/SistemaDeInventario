--
-- Archivo de Seed: Datos de Ejemplo para el Sistema de Inventario
-- Importar ESTE archivo DESPUÉS de haber importado schema.sql
--

-- Insertar Secciones de ejemplo
INSERT INTO `secciones` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Estantería A-1', 'Partes pequeñas de motor y componentes eléctricos.'),
(2, 'Patio Norte', 'Carrocería y partes grandes. Puertas, defensas, etc.'),
(3, 'Bodega Principal', 'Transmisiones, ejes y partes de suspensión.'),
(4, 'Sección de Llantas', 'Llantas y rines de varios tamaños.');

-- Insertar un usuario de tipo "Vendedor"
-- La contraseña es 'vendedor123'
INSERT INTO `usuarios` (`id`, `nombre_usuario`, `email`, `password_hash`, `rol_id`, `activo`) VALUES
(2, 'vendedor', 'vendedor@sistema.com', '$2y$10$EIZi1tN1U4dDqV8U5B9f3OwwpTAm6BfV6./u7.2Sg31r5yH5fIe.C', 2, 1);

-- Insertar Partes de ejemplo
-- Las imágenes son placeholders del servicio placehold.co
INSERT INTO `partes` (`id`, `nombre`, `descripcion`, `tipo_parte`, `marca_auto`, `modelo_auto`, `año_auto`, `precio`, `cantidad_disponible`, `imagen_url`, `thumbnail_url`, `seccion_id`) VALUES
(1, 'Faro Delantero Izquierdo', 'Faro de halógeno original, en buenas condiciones, con leves signos de uso.', 'Iluminación', 'Honda', 'Civic', 2018, 120.50, 5, 'https://placehold.co/800x600/EEE/31343C.png?text=Faro+Honda+Civic', 'https://placehold.co/400x300/EEE/31343C.png?text=Faro', 1),
(2, 'Puerta del Conductor', 'Puerta completa con panel interior y vidrio. Color rojo. Sin golpes mayores.', 'Carrocería', 'Toyota', 'Corolla', 2016, 350.00, 2, 'https://placehold.co/800x600/EEE/31343C.png?text=Puerta+Toyota', 'https://placehold.co/400x300/EEE/31343C.png?text=Puerta', 2),
(3, 'Alternador 90A', 'Alternador remanufacturado, probado y funcional. Compatible con varios modelos de Ford.', 'Eléctrico', 'Ford', 'Focus', 2014, 95.75, 8, 'https://placehold.co/800x600/EEE/31343C.png?text=Alternador+Ford', 'https://placehold.co/400x300/EEE/31343C.png?text=Alternador', 1),
(4, 'Llanta de Repuesto', 'Llanta de repuesto de tamaño completo, nunca usada. Marca Michelin 205/55 R16.', 'Llantas', 'Volkswagen', 'Jetta', 2020, 80.00, 1, 'https://placehold.co/800x600/EEE/31343C.png?text=Llanta+VW+Jetta', 'https://placehold.co/400x300/EEE/31343C.png?text=Llanta', 4),
(5, 'Caja de Transmisión Automática', 'Transmisión automática de 6 velocidades, extraída de un vehículo con bajo kilometraje.', 'Transmisión', 'Nissan', 'Sentra', 2019, 950.00, 1, 'https://placehold.co/800x600/EEE/31343C.png?text=Transmision+Nissan', 'https://placehold.co/400x300/EEE/31343C.png?text=Transmision', 3);

-- Insertar Comentarios de ejemplo
INSERT INTO `comentarios` (`id`, `parte_id`, `usuario_id`, `texto_comentario`, `estado`) VALUES
(1, 1, 2, '¿Esta parte es compatible con el modelo 2017?', 'aprobado'),
(2, 2, 2, 'Me interesa, ¿aún está disponible?', 'pendiente'),
(3, 1, 1, 'Sí, es compatible con los modelos 2016 a 2019.', 'aprobado');
