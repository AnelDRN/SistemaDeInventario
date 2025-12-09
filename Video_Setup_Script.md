# Guion de Video: Configuración del Repositorio 'Sistema de Inventario para Rastro de Autopartes'

**Duración Estimada:** 2-3 minutos
**Objetivo:** Guiar al usuario paso a paso en la instalación y puesta en marcha del proyecto desde un repositorio de GitHub.

---

### **[0:00 - 0:15] Introducción**

**(Narrador)** "¡Hola a todos! Soy [Tu Nombre/El Equipo] y hoy los guiaré en cómo configurar y ejecutar nuestro 'Sistema de Inventario para Rastro de Autopartes' desde cero, directamente desde GitHub. Este proyecto es una solución integral para la gestión de autopartes usadas, y con este video, lo tendrás funcionando en pocos minutos."

---

### **[0:15 - 0:45] Requisitos del Sistema (Prerrequisitos)**

**(Narrador)** "Antes de sumergirnos en la instalación, asegúrate de tener los siguientes prerrequisitos en tu máquina:"

*   **WAMP/LAMP/MAMP:** "Necesitas un entorno de desarrollo web local. Para usuarios de Windows, WAMP Server es una excelente opción. Si usas Linux o macOS, LAMP o MAMP son los equivalentes. Asegúrate de que Apache y MySQL estén configurados y listos para ejecutarse."
*   **PHP:** "Requerimos PHP versión 8.1 o superior."
*   **MySQL:** "Una base de datos MySQL 5.7 o superior, o su equivalente como MariaDB."

---

### **[0:45 - 1:45] Pasos de Instalación del Proyecto**

**(Narrador)** "Con los prerrequisitos cubiertos, sigamos estos sencillos pasos para instalar el proyecto:"

1.  **Clonar el Repositorio de GitHub:**
    *   **(Narrador)** "Primero, abre tu terminal o línea de comandos."
    *   **(Narrador)** "Navega al directorio donde tu servidor web WAMP aloja los proyectos. Usualmente es la carpeta `www` (ej. `C:\wamp64\www`)."
    *   **(En pantalla: mostrar el comando `git clone` y la URL del repositorio)** "Luego, ejecuta el siguiente comando para clonar el repositorio:"
        ```bash
        git clone https://github.com/tu-usuario/SistemaDeInventario.git SistemaDeInventario
        ```
    *   **(Narrador)** "Esto creará una carpeta llamada `SistemaDeInventario` con todos los archivos del proyecto."

2.  **Configuración de la Base de Datos:**
    *   **(Narrador)** "Ahora, vamos a preparar nuestra base de datos. Abre tu herramienta de gestión de bases de datos preferida, como phpMyAdmin o MySQL Workbench."
    *   **(En pantalla: mostrar interfaz de phpMyAdmin y el nombre de la DB)** "Crea una nueva base de datos y nómbrala `sistema_rastro`. Asegúrate de usar el cotejamiento `utf8mb4_unicode_ci`."
    *   **(En pantalla: mostrar la ruta del archivo `schema.sql`)** "Dentro de la carpeta `SistemaDeInventario/database` de tu proyecto, encontrarás un archivo llamado `schema.sql`. Impórtalo en tu nueva base de datos. Esto creará todas las tablas necesarias para la aplicación."
    *   **(Narrador, opcional)** "Si deseas poblar el sistema con datos de ejemplo para empezar a probar, también puedes importar el archivo `database/seed.sql`."

3.  **Instalar FPDF (Dependencia de PDF):**
    *   **(Narrador)** "Nuestro sistema utiliza la librería FPDF para generar facturas en PDF."
    *   **(En pantalla: mostrar la ruta `SistemaDeInventario/libs` y el comando `git clone`)** "Navega a la carpeta `libs` dentro de tu proyecto (`SistemaDeInventario/libs`) y ejecuta el siguiente comando:"
        ```bash
        git clone https://github.com/Setasign/FPDF.git fpdf
        ```
    *   **(Narrador)** "Esto descargará la librería FPDF en la ubicación correcta."

4.  **Configuración de PHP:**
    *   **(Narrador)** "Finalmente, un pequeño ajuste en PHP. Asegúrate de que la extensión `pdo_mysql` esté habilitada en tu archivo `php.ini`. Esto es crucial para la conexión a la base de datos MySQL."
    *   **(Narrador, opcional)** "Si vas a subir imágenes de gran tamaño, te recomendamos revisar y ajustar los valores de `upload_max_filesize` y `post_max_size` en tu `php.ini` a un valor adecuado, por ejemplo, 64 Megabytes."

---

### **[1:45 - 2:30] Accediendo al Sistema**

**(Narrador)** "¡Felicidades! Todos los pasos de instalación están completos."

*   **(Narrador)** "Asegúrate de que todos los servicios de tu WAMP/LAMP/MAMP (Apache, MySQL) estén iniciados."
*   **Acceso Público:** "(En pantalla: mostrar URL) Para acceder a la parte pública del sistema y navegar por el catálogo, abre tu navegador y ve a `http://localhost/SistemaDeInventario/public/`."
*   **Acceso Administrador:** "(En pantalla: mostrar URL y credenciales) Para el panel de administración, donde podrás gestionar el inventario y las ventas, visita `http://localhost/SistemaDeInventario/public/index.php?/login`. Las credenciales por defecto son `admin` como usuario y `root2514` como contraseña."

---

### **[2:30 - 3:00] Cierre**

**(Narrador)** "¡Y listo! Tu 'Sistema de Inventario para Rastro de Autopartes' ya está en funcionamiento. Esperamos que esta guía rápida te haya sido útil para poner el proyecto en marcha sin problemas."

"Para más detalles sobre las funcionalidades del sistema y su estructura, no olvides consultar el archivo `README.md` que se encuentra en la raíz del repositorio."

"¡Gracias por ver y hasta la próxima!"

---
