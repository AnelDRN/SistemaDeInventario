# Sistema de Inventario para Rastro de Autopartes

## Resumen del Proyecto

Este proyecto es un "Sistema de Inventario o Rastro" diseñado para la gestión integral de autopartes usadas, enfocado en optimizar el control de inventario, el proceso de ventas y la interacción con el cliente. El sistema sigue estrictamente los principios de Programación Orientada a Objetos (POO) y el patrón Modelo-Vista-Controlador (MVC), asegurando modularidad, seguridad y facilidad de mantenimiento.

**Objetivos Clave:**
*   **Gestión de Inventario:** CRUD completo para partes, incluyendo gestión de imágenes (con thumbnails automáticos) y secciones de ubicación física.
*   **Gestión de Usuarios y Roles:** Control de acceso y permisos, con "borrado lógico" para usuarios (no se eliminan, solo se desactivan).
*   **Procesamiento de Ventas:** Registro de ventas, disminución automática del inventario, y emisión de facturas (con cálculo de ITBMS).
*   **Página Pública (Catálogo):** Visualización de partes en venta con búsqueda, filtros, detalles de producto y sistema de comentarios.
*   **Carrito de Compras y Checkout:** Funcionalidad de carrito de compras y proceso de finalización de compra para clientes.
*   **Generación de Reportes:** Exportación de inventario y reportes de ventas (CSV, Excel).

**Principios de Diseño y Académicos:**
*   **POO:** Cada módulo funcional se encapsula en Clases dedicadas.
*   **Interfaces:** Utilizadas para el control de errores y la definición de contratos.
*   **Sanitización y Validación:** Clase independiente para la limpieza y validación de datos de entrada.
*   **Conexión a BD:** Gestionada mediante una Clase dedicada (Singleton).

## Base de Datos

El sistema utiliza **MySQL** con un cotejamiento `utf8mb4_unicode_ci`. La base de datos `sistema_rastro` se estructura en las siguientes tablas principales:

### Script de Creación de Tablas (`database/schema.sql`)

```sql
-- Estructura de la tabla `roles`
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

-- Estructura de la tabla `usuarios`
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

-- Estructura de la tabla `secciones`
CREATE TABLE IF NOT EXISTS `secciones` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL UNIQUE,
    `descripcion` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de la tabla `partes` (Inventario)
CREATE TABLE IF NOT EXISTS `partes` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(255) NOT NULL,
    `descripcion` TEXT,
    `tipo_parte` VARCHAR(100),
    `marca_auto` VARCHAR(100),
    `modelo_auto` VARCHAR(100),
    `anio_auto` INT,
    `precio` DECIMAL(10, 2) NOT NULL,
    `cantidad_disponible` INT NOT NULL DEFAULT 0,
    `imagen_url` VARCHAR(255),
    `thumbnail_url` VARCHAR(255),
    `seccion_id` INT NOT NULL,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estructura de la tabla `comentarios`
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

-- Estructura de la tabla `vendido_parte` (Para partes vendidas)
CREATE TABLE IF NOT EXISTS `vendido_parte` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `parte_original_id` INT NOT NULL,
    `nombre_parte` VARCHAR(255) NOT NULL,
    `precio_venta` DECIMAL(10, 2) NOT NULL,
    `usuario_vendedor_id` INT NOT NULL,
    `fecha_venta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`usuario_vendedor_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Relaciones Clave (Modelo E/R)
*   **Usuario - Rol:** Uno a muchos.
*   **Parte - Sección:** Uno a muchos.
*   **Venta - Usuario:** Uno a muchos (el usuario que "vende" la parte, en el contexto del sistema es el comprador final del carrito).
*   **Comentario - Parte/Usuario:** Uno a muchos.
*   **Comentario - Comentario (Auto-referencia):** Jerárquica para respuestas.

## Requisitos del Sistema

Para instalar y ejecutar el proyecto en un entorno de desarrollo local, se requiere la siguiente pila de software y pasos de configuración:

*   **Entorno:** WAMP (Windows, Apache, MySQL, PHP) o un entorno similar (LAMP/MAMP).
*   **Servidor Web:** Apache 2.4 o superior.
*   **Lenguaje de Programación:** PHP 8.1 o superior.
*   **Base de Datos:** MySQL 5.7 o superior (o MariaDB equivalente).

### Pasos de Instalación Adicionales:
1.  **Clonar el Repositorio:** Clone o descargue el código fuente del proyecto en el directorio `www` de su servidor WAMP (ej. `C:\wamp64\www\SistemaDeInventario`).
2.  **Configurar Base de Datos:**
    *   Cree una base de datos en PhpMyAdmin llamada `sistema_rastro` con el cotejamiento `utf8mb4_unicode_ci`.
    *   Importe el script `database/schema.sql`.
    *   (Opcional) Importe `database/seed.sql` para datos de ejemplo.
3.  **Instalar Dependencias:** El sistema utiliza la biblioteca **FPDF**. Clone su repositorio oficial en la carpeta `libs/` (`git clone https://github.com/Setasign/FPDF.git libs/fpdf`).
4.  **Configuración de PHP:**
    *   Asegúrese de que la extensión `pdo_mysql` esté activada en su `php.ini`.
    *   Verifique límites de subida (`upload_max_filesize`, `post_max_size`) para evitar errores al subir imagenes, estos cambios se deben realizar en
        "C:\wamp64\bin\apache\apache2.4.62.1\bin".
    *   `display_errors = Off` y `display_startup_errors = Off` para producción; `On` para depuración.

### Acceso al Sistema:
*   **Público:** `http://localhost/SistemaDeInventario/public/`
*   **Administración:** `http://localhost/SistemaDeInventario/public/index.php?/login` (Credenciales por defecto: `admin` / `root2514`)

## Video de Youtube

 **Link** : https://youtu.be/lhBYVqwFel8
 