
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
    - **Resumen:** Se ha realizado una reevaluación exhaustiva del proyecto contra `alcances2.md`, se han implementado numerosas funcionalidades pendientes y se ha abordado la mayoría de los puntos parcialmente cumplidos. Se resolvieron errores críticos de enrutamiento y búsqueda.
    - **Hitos:**
        - [x] **#1 Login:** Completado (funcional para administradores y clientes, incluye registro).
        - [x] **#2 CRUD de Usuarios (Funcionalidad básica y Cambio de Contraseña):** Completado (gestión básica de usuarios, módulo de cambio de contraseña e interfaz diferenciada para "Operadores" implementados).
        - [x] **#3 Módulo de registro de inventario (CRUD Inventario):** Completado (CRUD completo).
        - [x] **#4 Guardar thumbnail y imágenes grandes:** Completado (imágenes y thumbnails guardados y mostrados).
        - [x] **#7 Módulo de Secciones o Categorías:** Completado (CRUD de secciones).
        - [x] **#8 El Módulo de registro de inventario debe permitir consultas, búsquedas por nombre, por tipo de coche:** Completado (búsqueda por nombre/tipo de coche y filtrado por tipo de parte en admin y catálogo público).
        - [x] **#9 La conexión debe realizarse mediante una clase:** Completado.
        - [x] **#10 Implementar control de Errores:** Completado.
        - [x] **#11 Clase de sanitizar y validar datos:** Completado.
        - [x] **#12 Implementar una Interfaz como mínimo en el Proyecto:** Completado.
        - [x] **#14 Página pública dónde se puedan ver las partes:** Completado.
        - [x] **#16 Al entrar pueden tener algún tipo de categorías para dividir según el tipo de partes del auto:** Completado (filtrado por "tipo de parte" en catálogo público).
        - [x] **#17 Al darle clic a categoría aparezcan un listado con imágenes miniaturas de la partes del auto:** Completado.
        - [x] **#18 Que al darle clic a una opción o item del rastro puedan ver el detalle de la parte del auto, el costo y las unidades existentes:** Completado.
        - [x] **#20 Emitir factura con ITBMS:** Completado (Factura PDF con ITBMS calculado y desglosado).
        - [x] **#21 El sistema debe reducir el inventario al darse una compra de alguna parte del auto:** Completado.
        - [x] **#22 Cada módulo debe tener una clase con atributos, métodos y comportamientos:** Completado.
        - [x] **#23 El sistema debe contar con css y menús horizontales. Cada módulo debe permitir regresar al menú principal (HOME):** Completado.
        - [x] **Preparación de DB y modelos (`Role`/`User`) para permisos granulares.**
        - [x] **Configuración de `BaseController::hasPermission()` y `UserController::login()` para manejar permisos granulares.**
        - [x] **Implementación de permisos granulares en `RoleController` (gestión de roles).**
        - [x] **Implementación de permisos granulares en `SectionController` (gestión de secciones).**
        - [x] **Implementación de permisos granulares en `PartController` (gestión de inventario).**
        - [x] **Implementación de permisos granulares en `ReportController` (visualización de reportes).**
        - [x] **Implementación de permisos granulares en `SaleController` (gestión de ventas).**
        - [x] **Modificación del header del administrador para mostrar/ocultar enlaces basados en permisos de sesión.**
        - [x] **Implementación de Exportación de Inventario a CSV.**

    - **Siguiente Tarea:**
        - [ ] **#6 Permisos Granulares:** Evaluar si se requiere una implementación más profunda de permisos granulares para acciones muy específicas dentro de módulos (ej. "no puede borrar", "solo puede ver"). (Aunque la visibilidad de módulos principales ya está cubierta).
        - [ ] **#13 Reportes:** Implementar generación de reportes en formato **Excel** (no CSV).
        - [ ] **#15 & #19 Compra Virtual y Carrito de Compras:** **(FEATURE GRANDE - Se recomienda abordar como un proyecto separado):** Implementar las tablas de base de datos, lógica de sesión y funcionalidades básicas de un carrito de compras (añadir/quitar productos, ajustar cantidades) y desarrollar un proceso de "checkout".

    - **Notas:** Se recomienda al usuario ejecutar los scripts `ALTER TABLE` necesarios para actualizar la base de datos con los cambios de permisos granulares, según las instrucciones provistas anteriormente. La última versión del código fuente incluye la implementación de todos los permisos granulares y debería funcionar correctamente.```

