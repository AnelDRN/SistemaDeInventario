# Documentación Técnica: Sistema de Inventario o Rastro

**Versión:** 2.0
**Fecha:** 09 de Diciembre de 2025
**Autor:** Gemini, Arquitecto de Software

---

## 1. Requisitos de Instalación del Sistema y Requisitos No Funcionales

### 1.1. Requisitos de Instalación

Para instalar y ejecutar el proyecto en un entorno de desarrollo local, se requiere la siguiente pila de software y pasos de configuración:

*   **Entorno:** WAMP (Windows, Apache, MySQL, PHP) o un entorno similar (LAMP/MAMP).
*   **Servidor Web:** Apache 2.4 o superior.
*   **Lenguaje de Programación:** PHP 8.1 o superior.
*   **Base de Datos:** MySQL 5.7 o superior (o MariaDB equivalente).

**Pasos de Instalación:**

1.  **Clonar el Repositorio:** Clone o descargue el código fuente del proyecto en el directorio `www` de su servidor WAMP (ej. `C:\wamp64\www\SistemaDeInventario`).

2.  **Configurar Base de Datos:**
    *   Utilizando una herramienta como phpMyAdmin, cree una nueva base de datos llamada `sistema_rastro` con el cotejamiento `utf8mb4_unicode_ci`.
    *   Importe el script de estructura de tablas ejecutando el archivo `database/schema.sql`.
    *   (Opcional) Importe el script de datos de ejemplo ejecutando el archivo `database/seed.sql` para poblar el sistema con datos iniciales.

3.  **Instalar Dependencias:**
    *   El sistema utiliza la biblioteca **FPDF** para la generación de facturas en PDF. Clone el repositorio oficial en la carpeta `libs/`:
        ```bash
        git clone https://github.com/Setasign/FPDF.git libs/fpdf
        ```

4.  **Configuración de PHP:**
    *   Asegúrese de que la extensión `pdo_mysql` de PHP esté activada en su `php.ini`.
    *   Verifique que los límites para la subida de archivos en `php.ini` sean adecuados (ej. `upload_max_filesize = 64M` y `post_max_size = 64M`).

5.  **Acceder al Sistema:**
    *   Inicie los servicios de WAMP.
    *   Acceda al sitio público a través de `http://localhost/SistemaDeInventario/public/`.
    *   Acceda al portal de administración a través de `http://localhost/SistemaDeInventario/public/index.php?/login` con las credenciales por defecto: `admin` / `root2514`.

### 1.2. Requisitos No Funcionales

1.  **Rendimiento:** Las páginas del catálogo público y los detalles de las partes deben tener un tiempo de carga inferior a los 3 segundos en una conexión de banda ancha estándar. Las consultas a la base de datos deben estar optimizadas para respuestas rápidas.
2.  **Seguridad:** Toda la entrada de datos del usuario debe ser sanitizada para prevenir ataques XSS. Las contraseñas se almacenan utilizando algoritmos de hashing seguros (Bcrypt). El acceso a las rutas de administración debe estar protegido y ser accesible únicamente por usuarios con el rol de "Administrador".
3.  **Disponibilidad:** El sistema debe tener una disponibilidad del 99.9%, garantizando un acceso casi ininterrumpido para los clientes que consultan el catálogo y para los administradores que gestionan el inventario.
4.  **Usabilidad:** La interfaz pública debe ser intuitiva, permitiendo a los usuarios encontrar y entender la información de las partes fácilmente. El panel de administración debe ser claro y eficiente para que los empleados puedan gestionar el inventario y las ventas sin necesidad de una capacitación extensa.
5.  **Mantenibilidad:** El código está estructurado siguiendo estrictamente el patrón de diseño MVC, separando la lógica de negocio (Modelos), la presentación (Vistas) y el control de flujo (Controladores). Esto facilita la localización de errores, la implementación de nuevas funcionalidades y la actualización de componentes de forma independiente.

---

## 2. Modelado Funcional

### 2.1. Actores y Casos de Uso

*   **Actores Principales:**
    *   **Visitante:** Usuario anónimo que navega por el sitio.
    *   **Cliente Registrado:** Usuario que ha creado una cuenta.
    *   **Administrador:** Usuario con control total sobre el sistema.

*   **Casos de Uso Principales:**
    *   **Visitante:**
        *   Ver catálogo de partes.
        *   Buscar partes en el inventario.
        *   Ver detalles de una parte.
        *   Registrar una nueva cuenta de cliente.
    *   **Cliente Registrado:**
        *   (Hereda todos los casos de uso del Visitante).
        *   Publicar comentarios en las partes.
        *   Responder a otros comentarios.
    *   **Administrador:**
        *   (Hereda todos los casos de uso del Cliente Registrado).
        *   Gestionar inventario (CRUD de partes).
        *   Gestionar usuarios y roles.
        *   Gestionar secciones del rastro.
        *   Registrar una venta y generar factura PDF.
        *   Generar reportes de ventas mensuales.
        *   Eliminar cualquier comentario.

### 2.2. Descripción Textual de Caso de Uso Crítico

*   **Caso de Uso:** Registrar una Venta de Parte.
*   **Actor Principal:** Administrador.
*   **Precondición:** El Administrador ha iniciado sesión en el sistema. La parte a vender existe en el inventario y su cantidad disponible es mayor que cero.
*   **Flujo Normal:**
    1.  El Administrador navega a la sección de "Inventario" y localiza la parte que desea vender.
    2.  Hace clic en el botón "Vender" correspondiente a esa parte.
    3.  El sistema presenta un formulario de venta con los detalles de la parte y el precio sugerido.
    4.  El Administrador confirma o ajusta el "Precio de Venta Final" y hace clic en "Confirmar y Registrar Venta".
    5.  El sistema valida que el precio sea mayor que cero.
    6.  El sistema decrementa la `cantidad_disponible` de la parte en la tabla `partes`.
    7.  El sistema crea un nuevo registro en la tabla `vendido_parte` con los detalles de la transacción (ID de la parte, nombre, precio final, ID del vendedor y fecha).
    8.  El sistema genera una factura en formato PDF con los detalles de la venta y la ofrece para su descarga al Administrador.
*   **Postcondición:** El stock de la parte ha sido actualizado. La venta ha sido registrada permanentemente. Se ha descargado una factura de la venta.

---

## 3. Estructura del Sistema (Modelo Estructural)

El software está implementado siguiendo una arquitectura **MVC (Modelo-Vista-Controlador)**.

*   **Modelo:** Representa la lógica de negocio y los datos. Se encuentra en `src/Models` y contiene clases como `User.php`, `Part.php`, `Sale.php`, etc., que interactúan directamente con la base de datos.
*   **Vista:** Es la capa de presentación, encargada de mostrar la interfaz de usuario. Se encuentra en `views/`, separada en subdirectorios para `admin`, `public` y `layouts` (plantillas reutilizables).
*   **Controlador:** Actúa como intermediario entre el Modelo y la Vista. Recibe las peticiones del usuario, interactúa con el Modelo para obtener los datos y luego selecciona la Vista apropiada para mostrar la respuesta. Se encuentra en `src/Controllers`.

A continuación, la estructura jerárquica de los módulos principales:

*   `/` (Directorio Raíz)
    *   `public/`: Raíz del servidor web (DocumentRoot).
        *   `index.php`: **Front Controller**. Punto de entrada único que recibe todas las peticiones y las dirige al router.
    *   `src/`: Contiene toda la lógica de la aplicación.
        *   `Core/`: Clases centrales del framework (Router, BaseController).
        *   `Controllers/`: Controladores que manejan las peticiones (UserController, PartController, etc.).
        *   `Models/`: Modelos de datos que interactúan con la base de datos (User, Part, etc.).
        *   `Helpers/`: Clases de utilidad (Sanitizer, ImageHelper, InvoiceGenerator).
        *   `Interfaces/`: Contratos de PHP (IErrorHandler).
    *   `views/`: Contiene todas las plantillas de la interfaz de usuario.
        *   `admin/`: Vistas exclusivas del panel de administración.
        *   `public/`: Vistas para el sitio público (catálogo, detalle, registro).
        *   `layouts/`: Plantillas de cabecera y pie de página reutilizables.
    *   `database/`: Scripts SQL para la creación y población de la base de datos.
    *   `config/`: Archivos de configuración, como la conexión a la base de datos (`Database.php`).
    *   `libs/`: Bibliotecas de terceros (FPDF).
    *   `uploads/`: Directorio donde se almacenan las imágenes subidas por los usuarios.

---

## 4. Modelo Dinámico

A continuación se describe el flujo para la funcionalidad clave **"Añadir un nuevo comentario"**:

1.  **Vista (Petición del Usuario):**
    *   Un **Cliente Registrado** se encuentra en la página de detalle de una parte (`views/public/detalle.php`).
    *   Escribe un texto en el `<textarea>` del formulario de comentarios y hace clic en "Enviar Comentario".
    *   El formulario envía una petición `POST` a la URL `public/index.php?/part/comment`, incluyendo `part_id` y `comment_text`.

2.  **Front Controller y Router:**
    *   `public/index.php` recibe la petición.
    *   El objeto `Router` (`src/Core/Router.php`) asocia la URL `part/comment` con la acción `addComment` del `HomeController`.

3.  **Controlador (Lógica de Flujo):**
    *   Se ejecuta el método `addComment()` en `src/Controllers/HomeController.php`.
    *   El controlador verifica que el usuario tenga una sesión activa (`$_SESSION['user_id']`).
    *   Sanitiza los datos recibidos del formulario (`part_id` y `comment_text`) usando la clase `Sanitizer`.
    *   Crea una nueva instancia del modelo `Comment` (`new App\Models\Comment()`).
    *   Puebla el objeto `$comment` con los datos: `setParteId()`, `setUsuarioId()`, `setTextoComentario()`.

4.  **Modelo (Lógica de Negocio y Datos):**
    *   El controlador llama al método `$comment->save()`.
    *   Dentro del modelo `Comment.php`, el método `save()` construye una sentencia `INSERT` de SQL.
    *   El modelo obtiene la conexión a la base de datos a través de la clase Singleton `Database` y ejecuta la consulta para guardar el nuevo comentario en la tabla `comentarios`.

5.  **Respuesta al Usuario:**
    *   El método `save()` devuelve `true` al controlador.
    *   El controlador utiliza la clase `FlashMessage` para establecer un mensaje de éxito en la sesión (ej. "Comentario añadido con éxito.").
    *   Finalmente, el controlador redirige al usuario de vuelta a la página de detalle de la parte (`public/index.php?/part/ID_DE_LA_PARTE`), donde verá su nuevo comentario y el mensaje de éxito.

---

## 5. Base de Datos

### 5.1. Script de Creación de Tablas

El siguiente script SQL reconstruye la estructura de las tablas principales del sistema.

```sql
-- Tabla para los roles de usuario
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nombre_usuario` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol_id` INT NOT NULL,
    `activo` BOOLEAN NOT NULL DEFAULT TRUE,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla para el inventario de partes
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
    FOREIGN KEY (`seccion_id`) REFERENCES `secciones`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Tabla para los comentarios
CREATE TABLE IF NOT EXISTS `comentarios` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `parte_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `parent_id` INT NULL DEFAULT NULL,
    `texto_comentario` TEXT NOT NULL,
    `fecha_creacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`parte_id`) REFERENCES `partes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `comentarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla para el registro de ventas
CREATE TABLE IF NOT EXISTS `vendido_parte` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `parte_original_id` INT NOT NULL,
    `nombre_parte` VARCHAR(255) NOT NULL,
    `precio_venta` DECIMAL(10, 2) NOT NULL,
    `usuario_vendedor_id` INT NOT NULL,
    `fecha_venta` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`usuario_vendedor_id`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB;
```

### 5.2. Descripción de Relaciones (Modelo E/R)

*   **Usuario - Rol:** Un `Usuario` tiene un único `Rol`, pero un `Rol` puede estar asignado a muchos `Usuarios` (Relación uno a muchos).
*   **Parte - Sección:** Una `Parte` pertenece a una única `Seccion` de inventario. Una `Seccion` puede contener muchas `Partes` (Relación uno a muchos).
*   **Venta - Usuario:** Una `Venta` (`vendido_parte`) es registrada por un único `Usuario` (vendedor). Un `Usuario` puede registrar muchas `Ventas` (Relación uno a muchos).
*   **Comentario - Parte:** Un `Comentario` pertenece a una única `Parte`. Una `Parte` puede tener muchos `Comentarios` (Relación uno a muchos).
*   **Comentario - Usuario:** Un `Comentario` es escrito por un único `Usuario`. Un `Usuario` puede escribir muchos `Comentarios` (Relación uno a muchos).
*   **Comentario - Comentario (Auto-referencia):** Un `Comentario` puede ser una respuesta a otro `Comentario` (a través de `parent_id`). Un comentario padre puede tener muchas respuestas (Relación uno a muchos jerárquica).
