# Charla de Presentación: Sistema de Inventario para Rastro

**Título:** Gestión Integral de Inventario y Ventas para Rastro de Autopartes
**Duración Estimada:** 10-15 minutos
**Audiencia:** Equipo de desarrollo, stakeholders.
**Presentador:** [Tu Nombre]

---

## Guion de la Charla

### **(Minuto 0-1) Introducción y Agenda**

**Presentador:** "Buenos días a todos. Gracias por acompañarnos. Mi nombre es [Tu Nombre] y hoy les presentaré el 'Sistema de Inventario para Rastro de Autopartes', un proyecto que hemos desarrollado para modernizar y digitalizar la gestión de un negocio de venta de partes de autos usados."

"El objetivo principal de este sistema es doble: por un lado, optimizar el control de inventario y el proceso de ventas para los administradores del negocio. Por otro, ofrecer una vitrina digital para que los clientes puedan explorar y comprar productos de manera eficiente."

"Durante los próximos 10 a 15 minutos, cubriremos tres puntos clave:
1.  **La Arquitectura y el Diseño Técnico** detrás del sistema, incluyendo los diagramas UML esenciales.
2.  Un **Recorrido por las Funcionalidades Principales**, tanto para los clientes como para los administradores.
3.  Y finalmente, daremos paso a una **Demostración en Vivo** para que vean el sistema en acción."

---

### **(Minuto 1-4) Arquitectura y Diseño (UML)**

**Presentador:** "Para asegurar que el sistema fuera robusto, seguro y fácil de mantener, lo construimos sobre una base sólida utilizando una pila tecnológica probada: **PHP nativo en su versión 8**, sobre un servidor **Apache y MySQL**. La arquitectura de software que elegimos es el patrón **Modelo-Vista-Controlador (MVC)**."

*(Opcional: mostrar un diagrama simple de MVC)*

"En nuestro sistema:
*   **El Modelo** (`src/Models`) representa nuestros datos. Clases como `Part.php` o `User.php` se comunican directamente con la base de datos para leer y escribir información.
*   **La Vista** (`views/`) es todo lo que el usuario ve en su navegador. Tenemos vistas separadas para el catálogo público y el panel de administración, lo que nos permite tener interfaces completamente distintas para cada tipo de usuario.
*   **El Controlador** (`src/Controllers`) es el cerebro que conecta todo. Recibe una petición del usuario, le pide al Modelo los datos necesarios y se los entrega a la Vista para que los muestre."

"Para entender mejor el diseño funcional, veamos dos diagramas UML clave."

**1. Diagrama de Casos de Uso:**
"Este diagrama nos muestra quién interactúa con el sistema y qué puede hacer. Identificamos tres actores principales:"
*   "El **Administrador**, que tiene control total: gestiona el inventario, los usuarios, los roles y visualiza reportes."
*   "El **Cliente Registrado**, que puede interactuar con el catálogo, añadir productos al carrito, finalizar compras y, muy importante, participar en la comunidad dejando comentarios en los productos."
*   "Y el **Visitante Anónimo**, cuyo rol principal es explorar el catálogo y buscar partes, con la opción de registrarse para convertirse en cliente."

**2. Diagrama Entidad-Relación (Modelo de Datos):**
"El corazón de nuestro sistema es su base de datos. Las entidades más importantes son:"
*   `Partes`: Nuestro inventario. Cada registro es una pieza con su descripción, precio, stock y fotos.
*   `Usuarios` y `Roles`: Manejan el acceso y los permisos. Un usuario tiene un rol, y el rol define qué puede hacer, como 'gestionar inventario' o 'ver reportes'.
*   `Vendido_Parte`: Es nuestra bitácora de ventas. Cada vez que se completa una compra, se crea un registro aquí, asegurando que no perdamos el historial.
*   `Secciones` y `Comentarios`: Permiten organizar el inventario y fomentar la interacción del cliente, respectivamente."

"Esta estructura nos da una base de datos normalizada y eficiente."

---

### **(Minuto 4-9) Funcionalidades Principales**

**Presentador:** "Ahora, hagamos un recorrido rápido por las funcionalidades que hemos implementado, dividiéndolas por la experiencia de cada usuario."

**A. Experiencia del Cliente (Portal Público)**

*   **Catálogo Interactivo:** "El cliente es recibido con un catálogo visual de todas las partes disponibles. Puede realizar búsquedas por nombre, marca o tipo de auto, y también filtrar el inventario por secciones, como 'motores' o 'puertas'."
*   **Detalle del Producto:** "Al hacer clic en una parte, el cliente accede a una vista detallada con imágenes en alta resolución, descripción completa, stock disponible y precio. Desde aquí puede añadir el producto a su carrito."
*   **Carrito de Compras:** "Implementamos un carrito de compras completo gestionado por sesión. Los usuarios pueden añadir productos, ajustar cantidades, eliminar artículos y ver el total de su compra en tiempo real."
*   **Checkout Seguro:** "Una vez listo, el cliente puede finalizar su compra. El sistema le pedirá iniciar sesión (si no lo ha hecho), procesará la venta de forma segura actualizando el stock y registrará la transacción."
*   **Comunidad y Comentarios:** "Los clientes registrados pueden dejar comentarios y preguntas en las páginas de los productos, e incluso responder a otros usuarios, creando una comunidad activa."

**B. Experiencia del Administrador (Panel de Control)**

*   **Gestión de Inventario (CRUD):** "El administrador tiene control total sobre el inventario. Puede crear nuevas partes, subir imágenes (el sistema genera los thumbnails automáticamente), editar detalles y precios, o eliminar partes obsoletas."
*   **Gestión de Usuarios y Roles:** "Desde el panel puede gestionar todos los usuarios, cambiar sus roles (ej. de Cliente a Vendedor), y activar o desactivar cuentas gracias al borrado lógico, que nunca elimina un usuario de forma permanente."
*   **Sistema de Reportes:** "Una funcionalidad clave para el negocio. El administrador puede generar reportes de ventas mensuales para ver el rendimiento, y también exportar el inventario completo a formatos como CSV y Excel para análisis externos."
*   **Procesamiento de Ventas y Facturación:** "Además de las ventas online, el administrador puede registrar una venta manual directamente desde el inventario, generando al instante una factura en formato PDF, lista para ser impresa o enviada."

---

### **(Minuto 9-10) Conclusión y Transición a la Demo**

**Presentador:** "En resumen, hemos construido un sistema integral que no solo resuelve las necesidades operativas de un rastro de autopartes, sino que también crea un canal de ventas digital moderno y eficiente."

"La arquitectura MVC nos garantiza que el proyecto es escalable y fácil de mantener, y las funcionalidades implementadas cubren todo el ciclo de vida de una parte: desde su registro en el inventario hasta su venta final, ya sea en línea o de forma manual."

"Pero como dicen, 'ver para creer'."

"Así que ahora, para que puedan apreciar cómo todas estas piezas funcionan en conjunto, vamos a proceder con una demostración completa del sistema. Empezaremos desde la perspectiva de un cliente que busca una pieza y finalizaremos en el panel de administrador, revisando cómo se ha registrado esa venta."

*(Pausa, y preparación para iniciar la demostración en vivo).*

---
