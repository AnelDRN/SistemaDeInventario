
# **Documento Guía del Proyecto: Sistema de Inventario o Rastro**

**Versión:** 1.0
**Fecha:** 01 de Diciembre de 2025
**Facilitador:** Ing. Irina Fong

**Instrucciones para el Asistente de IA (Gemini CLI)**
- Este documento es la **única fuente de verdad**.
- Prioriza los requisitos marcados en **ROJO** en la documentación original (Sección 2.1).
- El sistema debe funcionar en WAMP.

---

**Sección 1: Arquitectura del Sistema**

- **Patrón:** MVC (Modelo-Vista-Controlador).
- **Enfoque:** Programación Orientada a Objetos (POO).
- **Navegación:** Menú Horizontal y Central. Botón "HOME" obligatorio en cada módulo (No usar 'Atrás' del navegador).

---

**Sección 2: Requisitos Funcionales y Técnicos (Prioridad Alta)**

**2.1 Requisitos Técnicos Obligatorios (Puntos 10-14):**
1.  **Conexión a BD:** Debe realizarse mediante una **Clase** dedicada (Singleton recomendado).
2.  **POO por Módulo:** Cada módulo (Usuarios, Partes, Ventas) debe tener su propia Clase con sus métodos y atributos.
3.  **Sanitización:** Debe existir una **Clase de Sanitizar y Validar Datos** independiente.
4.  **Control de Errores con Interfaces:** Implementar el manejo de errores utilizando **Interfaces** (ej. `interface ILogger`).
5.  **Estilos:** Uso de CSS y menús de navegación claros.

**2.2 Módulos Administrativos (Backend):**
1.  **Acceso:** Usuario Admin por defecto (`admin` / `root2514`).
2.  **Módulo Usuarios (Puntos 2 y 5):**
    - Roles y Permisos (Control total vs parcial).
    - CRUD (Altas, Actualizaciones, Consultas).
    - **Regla:** NO ELIMINAR usuarios. Usar "Borrado Lógico" (Activo: 1/0).
3.  **Módulo Inventario/Partes (Puntos 3, 6 y 7):**
    - Registro de Partes: Tipo (Puerta, Motor), Auto (Marca, Modelo, Año), Fecha Creación.
    - **Imágenes:** Guardar imagen grande y generar/guardar **Thumbnail**.
    - **Secciones:** Ubicación física del ítem en el rastro.
    - Consultas: Buscador de partes.
4.  **Módulo Ventas (Punto 4):**
    - Al vender, **disminuir inventario** y mover registro a tabla `VENDIDO_PARTE`.
    - Datos de venta: Qué se vendió, fecha, usuario vendedor, precio.

**2.3 Página Pública (Frontend - Puntos 8 y 9):**
1.  **Catálogo:** Listado de partes en venta (Foto Thumbnail + Extracto info).
2.  **Detalle:** Al hacer clic, ver Foto Grande, Costo, Unidades, Detalles completos.
3.  **Comentarios:**
    - Usuarios pueden comentar en las piezas.
    - **Moderación:** Los comentarios tienen estado (Publicar 1/0). Admin puede eliminar o aprobar.

---

**Sección 3: Estructura de Archivos**

```text
/sistema-rastro
├── public/             # DocumentRoot
│   ├── index.php       # Front Controller
│   ├── css/            # Estilos
│   ├── js/             # Scripts
│   └── uploads/        # Almacenamiento
│       ├── parts/      # Imágenes grandes
│       └── thumbs/     # Thumbnails generados
├── config/
│   └── Database.php    # Clase Conexión
├── src/
│   ├── Controllers/    # Lógica (UserController, PartController)
│   ├── Models/         # Datos (User, Part, Sale, Comment)
│   ├── Interfaces/     # IErrorHandler.php (Requisito 14)
│   ├── Helpers/        # Sanitizer.php (Requisito 13), ImageHelper.php
│   └── Core/           # Router, Controller base
├── views/
│   ├── layouts/        # Header, Nav, Footer
│   ├── admin/          # Paneles de gestión
│   └── public/         # Catálogo y detalles
└── database/           # Script SQL
```

---

**Sección 4: Pila Tecnológica**

- **Lenguaje:** PHP 8.x Nativo.
- **Base de Datos:** MySQL.
- **Servidor:** Apache (WAMP).
- **Frontend:** HTML5, CSS3 (Diseño propio o framework ligero(boostrap)), JS.

---

**Sección 8: Bitácora de Progreso**

*Esta sección se actualiza al final de cada sesión.*

**Sesión del 09/12/2025:**
    - **Resumen:** Se implementó la funcionalidad completa del carrito de compras y checkout, incluyendo la gestión de productos, cantidades, y un proceso de compra guiado. Se realizaron múltiples intentos de depuración y corrección de errores en el filtro de búsqueda del inventario administrativo.
    - **Hitos:**
        - [x] **#15 & #19 Compra Virtual y Carrito de Compras (Core):** Implementación de la lógica de sesión del carrito, añadir/actualizar/eliminar productos, y proceso de checkout con registro de ventas y actualización de stock.
        - [x] **Interfaz de Usuario para Carrito:** Creación de vistas para carrito y éxito de compra, y integración en la navegación pública.
        - [x] **Redirección Post-Login a Checkout:** Habilitada la funcionalidad para redirigir al usuario al checkout después de iniciar sesión si lo intentó previamente.
        - [x] **Manejo de Parámetros GET en Router:** Implementada una solución robusta en `public/index.php` para asegurar que los parámetros GET de la URL sean correctamente accesibles en los controladores.
        - [x] **Corrección de Error de Parámetros SQL (`Invalid parameter number`) en `Part::searchBySection`:** Identificado y corregido el error que impedía el correcto binding de parámetros en la consulta de búsqueda por sección.
        - [x] **Corrección de Errores de Cabeceras (`headers already sent`):** Identificada y corregida la salida prematura en `CartController.php` y el manejo de `display_errors` para una depuración más limpia.
        - [x] **Generación de Factura HTML para Checkout del Carrito:** Implementada la visualización en pantalla de la factura multi-ítem tras la compra.
        - [x] **Manejo de Errores de Carga de Clases (`InvoiceGenerator`):** Corregido el error `Class "App\Controllers\InvoiceGenerator" not found` en `CartController.php`.
        - [x] **Corrección de `Método ordersummary no encontrado`:** Reincorporados `orderSummary()` y `downloadInvoice()` en `CartController.php` tras una remoción accidental.
    - **Siguiente Tarea:**
        - [ ] **Investigación y Solución del Problema de Filtrado del Inventario Administrativo.**
        - [ ] **Investigación y Solución del Problema de Descarga de PDF de la Factura.**
    - **Notas:**
        - El problema de filtrado de búsqueda en `/admin/inventario` persiste según el último reporte del usuario. Aunque se han corregido los problemas de JS y de `$_GET`, y el error SQL de parámetros, la lista de resultados aún no se actualiza. La depuración muestra la consulta SQL y los parámetros correctos. Se requiere una investigación más profunda para determinar si el problema reside en la ausencia de datos coincidentes en la base de datos o en un problema de lógica en la visualización de los resultados.
        - La funcionalidad de descarga de PDF de la factura (`InvoiceGenerator::generateMultiItemPdf()`) no está operativa. Se han añadido logs de depuración para identificar la interrupción del proceso. La causa raíz aún no se ha identificado; se sospecha de un problema de "headers already sent" o de la configuración/compatibilidad de FPDF en el entorno.



