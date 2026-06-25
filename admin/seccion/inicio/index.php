<?php 
    include("../inicio/conexion.php");

    // Consultar el número de registros en las tablas principales
    $sentenciaCategorias = $conexion->prepare("SELECT COUNT(*) AS total_categorias FROM categorias");
    $sentenciaCategorias->execute();
    $totalCategorias = $sentenciaCategorias->fetch(PDO::FETCH_ASSOC)['total_categorias'];

    $sentenciaProductos = $conexion->prepare("SELECT COUNT(*) AS total_productos FROM productos");
    $sentenciaProductos->execute();
    $totalProductos = $sentenciaProductos->fetch(PDO::FETCH_ASSOC)['total_productos'];

    $sentenciaBlogs = $conexion->prepare("SELECT COUNT(*) AS total_blogs FROM blogs");
    $sentenciaBlogs->execute();
    $totalBlogs = $sentenciaBlogs->fetch(PDO::FETCH_ASSOC)['total_blogs'];

    $sentenciaServicios = $conexion->prepare("SELECT COUNT(*) AS total_servicios FROM servicios");
    $sentenciaServicios->execute();
    $totalServicios = $sentenciaServicios->fetch(PDO::FETCH_ASSOC)['total_servicios'];

    $sentenciaUsuarios = $conexion->prepare("SELECT COUNT(*) AS total_usuarios FROM usuarios");
    $sentenciaUsuarios->execute();
    $totalUsuarios = $sentenciaUsuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

    $sentenciaLocales = $conexion->prepare("SELECT COUNT(*) AS total_locales FROM locales");
    $sentenciaLocales->execute();
    $totalLocales = $sentenciaLocales->fetch(PDO::FETCH_ASSOC)['total_locales'];

    $sentenciaBanners = $conexion->prepare("SELECT COUNT(*) AS total_banners FROM banners");
    $sentenciaBanners->execute();
    $totalBanners = $sentenciaBanners->fetch(PDO::FETCH_ASSOC)['total_banners'];

    $sentenciaServiciosTable = $conexion->prepare("SELECT COUNT(*) AS total_servicios_table FROM servicio");
    $sentenciaServiciosTable->execute();
    $totalServiciosTable = $sentenciaServiciosTable->fetch(PDO::FETCH_ASSOC)['total_servicios_table'];

    $sentenciaRoles = $conexion->prepare("SELECT COUNT(*) AS total_roles FROM roles");
    $sentenciaRoles->execute();
    $totalRoles = $sentenciaRoles->fetch(PDO::FETCH_ASSOC)['total_roles'];

    $sentenciaRedesSociales = $conexion->prepare("SELECT COUNT(*) AS total_redes_sociales FROM redes_sociales");
    $sentenciaRedesSociales->execute();
    $totalRedesSociales = $sentenciaRedesSociales->fetch(PDO::FETCH_ASSOC)['total_redes_sociales'];

    $sentenciaMenuEnlaces = $conexion->prepare("SELECT COUNT(*) AS total_menu_enlaces FROM menu_enlaces");
    $sentenciaMenuEnlaces->execute();
    $totalMenuEnlaces = $sentenciaMenuEnlaces->fetch(PDO::FETCH_ASSOC)['total_menu_enlaces'];

    // Consultar el número de registros en la tabla usuarios (usuarios y clientes)
    $sentenciaUsuarios = $conexion->prepare("SELECT COUNT(*) AS total_usuarios FROM usuarios WHERE id_rol != 5");
    $sentenciaUsuarios->execute();
    $totalUsuarios = $sentenciaUsuarios->fetch(PDO::FETCH_ASSOC)['total_usuarios'];

    $sentenciaClientes = $conexion->prepare("SELECT COUNT(*) AS total_clientes FROM usuarios WHERE id_rol = 5");
    $sentenciaClientes->execute();
    $totalClientes = $sentenciaClientes->fetch(PDO::FETCH_ASSOC)['total_clientes'];

     // Banners
     $sentenciaBanners = $conexion->prepare("SELECT COUNT(*) AS total_banners FROM banners");
     $sentenciaBanners->execute();
     $totalBanners = $sentenciaBanners->fetch(PDO::FETCH_ASSOC)['total_banners'];
 
     // Locales
     $sentenciaLocales = $conexion->prepare("SELECT COUNT(*) AS total_locales FROM locales");
     $sentenciaLocales->execute();
     $totalLocales = $sentenciaLocales->fetch(PDO::FETCH_ASSOC)['total_locales'];
 
     // Pedidos
     $sentenciaPedidos = $conexion->prepare("SELECT COUNT(*) AS total_pedidos FROM pedidos");
     $sentenciaPedidos->execute();
     $totalPedidos = $sentenciaPedidos->fetch(PDO::FETCH_ASSOC)['total_pedidos'];

    include("../../templates/header_admin.php"); 
?>

<div class="container-fluid">
    <div class="row align-items-md-stretch">
        <div class="col-md-12">
            <div class="h-100 p-5 border rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="titulo_categoria">
                    Bienvenido(a) al <span class="text_red">Administrador</span>, <?php echo $_SESSION['nombres'] ?>
                </div>
                <p>Este espacio es para administrar su sitio web, elija una opción:</p>
            </div>
        </div>
    </div>
    <div class="row mt-3">

    <?php if (tiene_permiso_leer('categorias')): ?>
    <a href="../categorias/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-primary d-flex align-items-center justify-content-center">
                    <i class="ri-folder-open-line ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">SubCategorías</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalCategorias; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('productos')): ?>
    <a href="../productos/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-secondary d-flex align-items-center justify-content-center">
                    <i class="ri-store-line ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Productos</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalProductos; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('blogs')): ?>
    <a href="../blogs/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-success d-flex align-items-center justify-content-center">
                    <i class="ri-pencil-ruler-2-line ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Blogs</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalBlogs; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('servicios')): ?>
    <a href="../servicios/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-warning d-flex align-items-center justify-content-center">
                    <i class="ri-service-fill ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Servicios</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalServiciosTable; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('banners')): ?>
    <a href="../banners/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-warning d-flex align-items-center justify-content-center">
                    <i class="ri-image-line ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Banners</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalBanners; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('roles')): ?>
    <a href="../roles/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-info d-flex align-items-center justify-content-center">
                    <i class="ri-user-star-fill ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Roles</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalRoles; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('redes_sociales')): ?>
    <a href="../redes_sociales/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-dark d-flex align-items-center justify-content-center">
                    <i class="ri-facebook-fill ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Redes Sociales</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalRedesSociales; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('usuarios')): ?>
    <a href="../usuarios/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-primary d-flex align-items-center justify-content-center">
                    <i class="ri-user-line ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Usuarios</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalUsuarios; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>

<?php if (tiene_permiso_leer('locales')): ?>
    <a href="../locales/" class="col-lg-3 col-md-6 mb-4 text-decoration-none">
        <div class="card h-100 rounded-0" style="cursor: pointer;">
            <div class="row g-0 h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="col-5 bg-info d-flex align-items-center justify-content-center">
                    <i class="ri-store-2-line ri-3x text-white"></i>
                </div>
                <div class="col-7">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center h-100">
                        <h5 class="card-title text-center">Locales</h5>
                        <h4 class="card-text text-center">Total: <?php echo $totalLocales; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </a>
<?php endif; ?>


        
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
