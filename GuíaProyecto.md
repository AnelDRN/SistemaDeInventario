
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

- **Estado Inicial:**
    - **Resumen:** Inicio del proyecto. Definición de requerimientos basada en documento PDF.
    - **Posición Actual:** Fase 0 - Configuración.
    - **Siguiente Tarea Inmediata:**
        - [ ] Crear estructura de carpetas en `www`.
        - [x] Diseñar Script SQL (Tablas: `usuarios`, `roles`, `partes`, `secciones`, `vendido_parte`, `comentarios`).
```

