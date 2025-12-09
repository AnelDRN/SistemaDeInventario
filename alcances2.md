1	Login
2	CRUD de módulos de Usuarios Administrativos o Operadores del Sisetma. Módulo de Usuarios debe permitir agregar, modificar, buscar y  deshabilitar los usuarios  (Activo: posibles estados (1,0)). Se propone dos usuarios, un Usuario Administrador y un Usuario Operador del Sistema que sea el que alimenta el inventario. Cuándo inicie sessión un usuario operador no puede ver la sección de Módulo de Usuarios. Pero si podrá ver las otras opciones, incluyendo un módulo para cambio de password.
3	Módulo de registro de inventario de partes de autos. Aquí se levantan el inventario de lo que tenemos, puertas, motor, retrovisor, vidrio, precio, especificando de que auto, marca, modelo, año. Fecha de Creación. CRUD Inventario.
4	Debe permitir guardar thumbnail de las partes del auto,  y las imagenes grandes para  mostrarlas luego en un listado, las partes y la imagen miniatura, con información resumida, al darle clic debe ir al detalle de la parte del auto que está ofertanto.
6	Módulos de Roles (Permisos - Alcance). Los usuarios pueden tener distintos roles. Algunos pueden tener el control total o solo algunos módulos.
7	Módulo  de Secciones o Categorías según dónde se ubique el item de las partes del auto.
8	El Módulo de registro de inventario debe permitir consultas, búsquedas por nombre, por tipo de coche.
9	La conexión debe realizarse medinate una clase.
10	Implementar control de Errores. (control de erróres en el login)
11	Debe contar con una clase de sanitizar y validar datos.
12	Implementar una Interfaz como mínimo en el Proyecto.
13	 El sistema debe generar reportes de excel del inventario actual, puede ser filtrado por categoría o por otro filtro.  El sistema debe generar estadísticas de ventas por mes. Generar el total por Categoría. Mostrar cuales son las partes del auto más vendidas.
    
Página Pública	
14	Tener una página pública dónde se puedan ver las partes de los autos con los que cuenta el rastro. 
15	Una sección dónde los posibles compradores pueden ingresar y logearse para hacer la compra virtual. 
16	Al entrar pueden tener algún tipo de categorías para dividir según el tipo de partes del auto.
17	Al darle clic a categoría aparezcan un listado con imágenes miniaturas de la partes del auto.
18	Que al darle clic a una opción o item del rastro puedan ver el detalle de la parte del auto, el costo y las unidades existentes.
19	Pueden tener un carrito, e ir agregando al carrito las cosas que van comprando, y la cantidad.
20	El sistema debe emitir una factura con la descripción de la compra. Fecha que se realizó la venta y la venta con su descripción. Y calcular el 7% de itbms.
21	El sistema debe reducir el inventario al darse una compra de alguna parte del auto. O sea la cantidad en existencia.
22	Cada módulo debe tener una clase con atributos, métodos y comportamientos.
23	El sistema debe contar con css y menús horizontales. Cada módulo debe permitir regresar al menú principal (HOME).
