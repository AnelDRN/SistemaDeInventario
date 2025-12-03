Alcance general del sistema

1	Documentación del Sistema con UML.
2	Utilizar clases para cada módulo solicitado (Interfaces en el login o control de errores.)
3	Lo que permita tener una estructura ordenada de Modelos (Conexiones, Clases que integren las operaciones en las tablas)
	Vista (Formulario). Separar los componentes de la aplicación dependiendo de la responsabilidad que tienen
4	Cumplir con los requisitos del Proyecto
5	Incluir un Video explicando el Proyecto, compartir el URL en Moodle y dentro de la documentación.
7	Si algún integrante se le pregunta algo y no sabe se le descuenta (5%) al integrante.
8	Copiar el URL del repositorio en el proyecto (github).  El gitthub debe cumplir con un Resumen indicando los requisitos, bien documentado. Debe incluir base de datos. Requisitos del Sistema.
	
	
1	Presentación Preliminar de la Documentación del Sistema con UML
2	Presentación Preliminar del Proyecto (Avances del Proyecto)

Alcance	 de Módulos del sistema
1	Login
2	CRUD de módulos de Usuarios Administrativos. Módulo de Usuarios (Altas, Actualizaciones y Consultas). No se deben eliminar usuarios, se pueden desactivar (Activo: posibles estados (1,0)
3	Módulo de registro de inventario de partes de autos. Aquí se levantan el inventario de lo que tenemos, puertas, motor, retrovisor, vidrio, especificando de que auto, marca, modelo, año. Fecha de Creación.
	CRUD del Auto o partes del Auto. Parte del auto,  (marca del auto), año. Información del año del auto. Debe permitir guardar thumbnail de las partes del auto,  y las imagenes grandes.
4	Para disminuir el inventario se debe enviar a una tabla de VENDIDO_PARTE (Puede tener otro nombre), en esta tabla debe enviarse que se vendió, que fecha, que usuario, y a que precio.
5	Módulos de Roles (Permisos - Alcance). Los usuarios pueden tener distintos roles. Algunos pueden tener el control total o solo algunos módulos.
6	Módulo  de Secciones según dónde se ubique el item de las partes del auto.
7	El Módulo de registro de inventario debe permitir consultas.
	
Alcance de Página Pública	
8	Tener una página pública dónde se puedan ver las partes de los autos con los que cuenta el rastro. Como un listado con thumbnail de la parte del auto que está en venta.
	con un pequño extracto de información del auto.
9	Que al darle clic a una opción o item del rastro puedan ver el detalle de la parte del auto, el costo y las unidades existentes.
	Al darle clic a alguna de estos registros, debe aparecer un detalle de la imagen, el costo  y la información de la parte del coche.
	En el detalle de la parte del auto, el usuario puede ver las observaciones, comentarios de los que han visitado esa página.
	O sea que debe permitir agregar comentarios. Estos comentarios pueden ser eliminado o tener una opción de permitir Publicar (1,0).
	
10	Cada módulo debe tener una clase con las posibles acciones que les sucedan a sus atributos.
	El sistema debe contar con css y menús horizontales. Cada módulo debe permitir regresar al menú principal (HOME)
11	La conexión debe realizarse medinate una clase.
12	Implementar control de Errores.
13	Debe contar con una clase de sanitizar y validar datos.
14	Utilizar Interfaces para control de errores. 
Crear un usuario general administrativo llamado   admin y la contraseña será root2514	