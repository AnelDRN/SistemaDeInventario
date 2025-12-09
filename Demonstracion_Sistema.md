# Guion de Demostración: Sistema de Inventario para Rastro de Autopartes

**Duración:** 15 minutos
**Público:** Equipo interno, stakeholders, potenciales usuarios.
**Objetivo:** Mostrar las funcionalidades clave del sistema y su facilidad de uso, tanto para clientes como para administradores.

---

### **1. Introducción Rápida (0-1 minuto)**

**(Presentador)** "Buenos días/tardes a todos. Hoy les presentaré nuestro 'Sistema de Inventario para Rastro de Autopartes', una solución integral diseñada para modernizar la gestión y venta de piezas usadas. Veremos cómo facilita la experiencia del cliente y optimiza las operaciones administrativas."

---

### **2. Experiencia del Cliente (Portal Público) (4-5 minutos)**

**(Presentador)** "Comenzaremos desde la perspectiva de un cliente o visitante anónimo, explorando el catálogo de partes."

*   **Página de Inicio / Catálogo (Home):**
    *   "Esta es nuestra página principal, donde los usuarios pueden ver el inventario disponible. Se presenta de forma visual con imágenes y un resumen de cada parte."
    *   *(Navegar por la página, mostrar un par de artículos).*

*   **Búsqueda y Filtros:**
    *   "Para encontrar una pieza específica, el sistema ofrece una potente funcionalidad de búsqueda."
    *   *(En el campo de búsqueda, escribir 'Faro' o 'Motor' y presionar Enter/clic en buscar.)* "Aquí podemos buscar por nombre, tipo de parte, marca o modelo de auto. Como ven, los resultados se filtran instantáneamente."
    *   *(Mostrar también el filtro por 'Tipo de Parte' si hay datos de ejemplo que lo permitan.)* "También podemos filtrar por tipo de parte, si buscamos algo más general como 'transmisión'."

*   **Página de Detalle del Producto:**
    *   *(Hacer clic en una parte relevante, idealmente una que tenga 'Faro' en el nombre o que se usó en la búsqueda.)* "Al hacer clic en una parte, accedemos a su página de detalle. Aquí encontramos imágenes de alta resolución, una descripción completa, el precio, las unidades disponibles y el auto al que pertenece."
    *   *(Mostrar la sección de comentarios.)* "Los clientes registrados pueden dejar comentarios y participar en discusiones sobre la pieza."

*   **Añadir al Carrito de Compras:**
    *   *(En la página de detalle, añadir el producto al carrito. Si hay stock, poner una cantidad como '1' o '2'.)* "Desde aquí, podemos añadir la pieza a nuestro carrito de compras. El sistema valida el stock disponible."
    *   *(Mostrar el mensaje de éxito '¡X fue agregado al carrito!' y el contador del carrito en el header.)* "Vemos la confirmación y cómo el contador del carrito se actualiza en tiempo real."

*   **Ver Carrito de Compras:**
    *   *(Hacer clic en el ícono del carrito en el header.)* "Esta es la vista de nuestro carrito. Podemos ver un resumen de las piezas que hemos añadido, con sus cantidades, precios unitarios y subtotales. Si cambiamos de opinión, podemos actualizar las cantidades..." *(Ajustar una cantidad y mostrar cómo se actualiza el subtotal)* "...o eliminar una pieza por completo." *(Eliminar un ítem, mostrar mensaje de éxito y total actualizado)*

---

### **3. Proceso de Checkout y Facturación (2-3 minutos)**

**(Presentador)** "Una vez que el cliente está satisfecho con su selección, procede a finalizar la compra."

*   **Iniciar Checkout / Login de Cliente:**
    *   *(Hacer clic en 'Finalizar Compra' en el carrito.)* "El sistema nos pedirá que iniciemos sesión si aún no lo hemos hecho." *(Iniciar sesión con una cuenta de cliente de prueba: `cliente` / `password` o similar. Si ya se inició sesión antes, omitir esto y mencionar que el sistema lo gestiona automáticamente.)* "Para continuar, iniciamos sesión como cliente."
    *   *(Si hay redirección después del login, mencionar cómo el sistema nos devuelve al checkout.)*

*   **Resumen del Pedido y Factura en Pantalla:**
    *   *(Mostrar la página de 'Resumen de tu Pedido y Factura'.)* "¡La compra se ha completado! Aquí vemos una factura detallada de nuestro pedido, directamente en la pantalla. Incluye el ID del pedido, la fecha, el nombre del cliente y un desglose de todos los artículos."
    *   *(Señalar el cálculo del ITBMS.)* "El sistema calcula automáticamente el 7% de ITBMS sobre el subtotal y muestra el total final."

*   **Descarga de Factura en PDF:**
    *   *(Hacer clic en 'Descargar Factura (PDF)' y mostrar cómo se descarga o abre el PDF.)* "Y para mayor comodidad, podemos descargar una copia de esta factura en formato PDF. Es ideal para llevar un registro o imprimirla." *(Confirmar que el PDF se descarga correctamente y mostrar el archivo descargado si es posible).*

---

### **4. Experiencia del Administrador (Panel de Control) (5-6 minutos)**

**(Presentador)** "Ahora, cambiemos de rol y veamos las poderosas herramientas que el sistema ofrece a los administradores del rastro."

*   **Login de Administrador:**
    *   *(Cerrar sesión como cliente.)* "Cerramos la sesión de cliente y accedemos como administrador." *(Iniciar sesión con la cuenta de admin: `admin` / `root2514`)*

*   **Dashboard / Gestión de Inventario:**
    *   *(Navegar al 'Admin Dashboard' si existe, o directamente a 'Gestión de Inventario'.)* "Este es el panel principal para los administradores. Aquí tenemos acceso a todas las funcionalidades de gestión."
    *   *(Ir a 'Gestión de Inventario'.)* "La piedra angular del sistema es la gestión de nuestro inventario de partes."
    *   *(Mostrar la lista de partes.)* "Podemos ver todas las partes disponibles, con imágenes, cantidades y secciones."
    *   *(Demostrar el filtro de búsqueda en el admin, buscando 'Faro' o por una sección.)* "Al igual que en el lado del cliente, el administrador puede buscar y filtrar partes para encontrar rápidamente lo que necesita." *(Confirmar que ahora sí se filtra)*
    *   *(Hacer clic en 'Editar' en una parte.)* "Podemos editar fácilmente los detalles de cualquier parte, actualizar precios, stock o incluso cambiar su imagen. El sistema genera los thumbnails automáticamente al subir una imagen." *(Cambiar un precio o stock y guardar.)*
    *   *(Mencionar brevemente las opciones de 'Añadir Nueva Parte' y 'Borrar', sin ejecutarlas completamente para ahorrar tiempo.)*

*   **Gestión de Usuarios:**
    *   *(Navegar a 'Gestión de Usuarios'.)* "El administrador tiene control sobre los usuarios del sistema. Puede añadir nuevos usuarios, modificar sus roles..." *(Mencionar la existencia de roles como 'Administrador', 'Vendedor', 'Cliente').*
    *   *(Demostrar el 'borrado lógico' de un usuario.)* "Y muy importante, si un usuario necesita ser inhabilitado, utilizamos un 'borrado lógico', lo que significa que la cuenta se desactiva pero no se elimina permanentemente del sistema, preservando la integridad de los datos." *(Desactivar un usuario de prueba, mostrar mensaje de éxito.)*

*   **Reportes de Ventas:**
    *   *(Navegar a 'Reportes'.)* "Para la toma de decisiones, el sistema ofrece reportes de ventas."
    *   *(Mostrar el reporte mensual.)* "Podemos generar reportes mensuales de ventas, viendo los ingresos totales, por categoría y las partes más vendidas."
    *   *(Hacer clic en 'Exportar a CSV' o 'Exportar a Excel' si esa funcionalidad está operativa.)* "Y podemos exportar estos datos a formatos como CSV o Excel para un análisis más profundo." *(Mencionar el estado del filtro de exportación si se intentó arreglar o si funciona.)*

*   **Venta Manual (si hay tiempo):**
    *   *(Volver a 'Gestión de Inventario', seleccionar una parte con stock, y hacer clic en 'Vender'.)* "Además de las compras online, un administrador puede registrar una venta manual directamente desde el inventario. El sistema descuenta el stock y genera una factura al instante." *(Mostrar el formulario de venta y mencionar que se generaría un PDF.)*

---

### **5. Conclusión (0-1 minuto)**

**(Presentador)** "Para resumir, hemos visto un sistema robusto y fácil de usar que cubre todas las necesidades esenciales para un rastro de autopartes. Desde la navegación del cliente y la experiencia de compra online con su factura, hasta la gestión completa del inventario, usuarios y reportes para el administrador."

"Nuestro enfoque MVC y el uso de tecnologías estándar nos aseguran una plataforma mantenible y escalable para el futuro crecimiento del negocio."

"Gracias por su atención. ¿Hay alguna pregunta?"

---
