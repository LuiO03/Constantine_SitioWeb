# Constantine Sitio Web

Sistema web en PHP para la tienda de ropa Constantine. El proyecto combina un catálogo público, carrito de compras, pedidos, blog, contacto y secciones legales, junto con un panel administrativo para gestionar productos, banners, categorías, usuarios, clientes, locales, servicios, mensajes y pedidos.

## Descripción general

La aplicación está pensada como una tienda online con navegación por públicos y categorías, fichas de producto, carrusel de banners en la portada y módulos de administración para controlar el contenido del sitio y la operación comercial.

En la portada se cargan dinámicamente desde la base de datos:

- banners activos de la página de inicio
- categorías de producto
- productos por público
- colecciones destacadas por orden de creación y precio

## Funcionalidades visibles

### Sitio público

- Inicio con banners y secciones de productos
- Catálogo de productos con filtros por público y categoría
- Vista de detalle de producto
- Carrito de compras
- Confirmación y gestión del pedido
- Blog
- Contacto
- Nosotros
- Servicios
- Secciones legales: políticas de privacidad, términos y condiciones y libro de reclamaciones

### Panel administrativo

- Inicio de sesión, registro y cierre de sesión
- Gestión de banners
- Gestión de blogs
- Gestión de categorías
- Gestión de clientes
- Gestión de contactos
- Gestión de locales
- Gestión de mensajes
- Gestión de pedidos
- Gestión de productos y variantes
- Gestión de públicos
- Gestión de roles y permisos
- Gestión de servicios
- Gestión de usuarios

## Tecnologías utilizadas

- PHP con PDO
- MySQL / MariaDB
- HTML, CSS y JavaScript
- Sesiones para autenticación
- Librerías locales: FPDF y PhpSpreadsheet
- Recursos externos por CDN: Remixicon, jQuery, Owl Carousel y Animate.css

## Estructura principal

- [index.php](index.php) punto de entrada del sitio público
- [layout/header.php](layout/header.php) encabezado general y navegación
- [layout/footer.php](layout/footer.php) pie de página
- [admin/seccion/inicio/conexion.php](admin/seccion/inicio/conexion.php) conexión a la base de datos
- [db/constantine_bd.sql](db/constantine_bd.sql) esquema y datos iniciales
- [productos/](productos/) catálogo y detalle de productos
- [carrito/](carrito/) flujo del carrito y confirmación de compra
- [blog/](blog/) blog público
- [contactos/](contactos/) formulario de contacto
- [nosotros/](nosotros/) información institucional
- [servicios/](servicios/) servicios de la marca
- [legal/](legal/) páginas legales
- [admin/seccion/](admin/seccion/) panel administrativo

## Base de datos

El proyecto incluye un script de creación y carga inicial en [db/constantine_bd.sql](db/constantine_bd.sql). Entre las tablas principales están:

- usuarios, roles y permisos
- banners, blogs, categorías y públicos
- productos, colores, tallas y productos_variantes
- carrito, pedidos y pedidos_productos
- contactos_negocio, redes_sociales, locales y formulario_contacto

El script también crea triggers para recalcular el stock total de productos cuando cambian sus variantes.

## Instalación local

1. Clona o copia el proyecto dentro de `htdocs` de XAMPP o en una carpeta servida por Apache.
2. Crea la base de datos importando [db/constantine_bd.sql](db/constantine_bd.sql).
3. Revisa la conexión en [admin/seccion/inicio/conexion.php](admin/seccion/inicio/conexion.php) y ajusta usuario, contraseña y nombre de base de datos si hace falta.
4. Verifica que las carpetas `images/`, `libs/` y `admin/` estén completas.
5. Abre el proyecto en el navegador desde `http://localhost/Constantine_SitioWeb/`.

## Observaciones técnicas

- El proyecto usa una configuración de URL base que cambia entre entorno local y producción.
- Hay recursos visuales y dependencias cargadas desde CDN, por lo que el sitio necesita acceso a internet para algunos componentes.
- La base de datos contiene datos de ejemplo para banners, roles, usuarios, categorías, públicos, locales, blog y servicios.

## Notas detectadas en el análisis

- En la portada, el bloque de “Lo más vendido” está ordenado por precio de venta, no por una métrica real de ventas.
- El encabezado de usuario referencia una variable de pedido pendiente que no se define en ese archivo.
- El script SQL contiene dos tablas distintas para servicios: `servicio` y `servicios`.
- En la tabla de públicos hay una inversión en las descripciones de “Niños” y “Niñas”.

## Estructura resumida

```text
Constantine_SitioWeb/
├── admin/
├── blog/
├── carrito/
├── contactos/
├── db/
├── images/
├── layout/
├── legal/
├── libs/
├── nosotros/
├── productos/
├── servicios/
└── style/
```

## Recomendación

Si vas a publicar el proyecto, conviene mover las credenciales de base de datos a un archivo de configuración fuera del repositorio y revisar las inconsistencias del script SQL antes de desplegar.