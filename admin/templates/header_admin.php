<?php 

    if (!isset($_SESSION["nombres"])) {
        header("Location: ../inicio/cerrar.php");
        exit();
    }

    error_reporting(0);

    /*
    $varsesion = $_SESSION['usuario'];

    if ($varsesion == null || $varsesion == '') {
        echo "No tienes autorizacion...";
        die();
    }
    */

    if ($_SERVER['HTTP_HOST'] === 'localhost') {
        // URL local XAMPP
        $url_base = "http://localhost/Constantine_SitioWeb/";
    } else {
        // URL producción InfinityFree
        // $url_base = "https://luiosorio.lovestoblog.com/Constantine_SitioWeb/";
        $url_base = "https://constantine.infinityfreeapp.com/";
    }

    $directorioActual = basename(dirname($_SERVER['PHP_SELF']));

    $id_usuario = $_SESSION['id_usuario'];

    // Consultar el id_enlace correspondiente
    $sql_id_enlace = "SELECT id_enlace FROM menu_enlaces WHERE nombre = :nombre";
    $sentencia = $conexion->prepare($sql_id_enlace);
    $sentencia->bindParam(':nombre', $directorioActual, PDO::PARAM_STR);
    $sentencia->execute();
    $id_enlace = $sentencia->fetchColumn();

    if ($id_enlace) {
        // Consulta para verificar permisos
        $id_usuario = $_SESSION['id_usuario']; // Usuario logueado
        $sql_permisos = "SELECT p.leer, p.crear, p.editar, p.eliminar
                        FROM permisos p
                        JOIN roles r ON p.id_rol = r.id_rol
                        JOIN usuarios u ON u.id_rol = r.id_rol
                        WHERE u.id_usuario = :id_usuario AND p.enlace_id = :id_enlace";
        $sentencia_permisos = $conexion->prepare($sql_permisos);
        $sentencia_permisos->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $sentencia_permisos->bindParam(':id_enlace', $id_enlace, PDO::PARAM_INT);
        $sentencia_permisos->execute();

        $permiso = $sentencia_permisos->fetch(PDO::FETCH_ASSOC);
    }

    //============== verifica si el usuario tiene permiso para leer ======================//
    $id_rol = $_SESSION['id_rol'];
    $enlace_actual = basename(dirname($_SERVER['PHP_SELF'])); // Obtiene el nombre del archivo actual

    // Verificar si el rol tiene permiso para "leer" esta página
    $sentencia = $conexion->prepare("SELECT COUNT(*) FROM permisos 
        INNER JOIN menu_enlaces ON permisos.enlace_id = menu_enlaces.id_enlace
        WHERE permisos.id_rol = :id_rol AND menu_enlaces.nombre = :enlace AND permisos.leer = 'visible'
    ");
    $sentencia->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
    $sentencia->bindParam(':enlace', $enlace_actual, PDO::PARAM_STR);
    $sentencia->execute();
    $tiene_permiso = $sentencia->fetchColumn();

    if (!$tiene_permiso) {
        header("Location: ../../templates/acceso_denegado.php");
        exit();
    }

    function tiene_permiso_leer($enlace) {
        global $conexion, $id_usuario;
    
        // Obtener el id_enlace para el enlace actual
        $sql_id_enlace = "SELECT id_enlace FROM menu_enlaces WHERE nombre = :nombre";
        $sentencia = $conexion->prepare($sql_id_enlace);
        $sentencia->bindParam(':nombre', $enlace, PDO::PARAM_STR);
        $sentencia->execute();
        $id_enlace = $sentencia->fetchColumn();
    
        if ($id_enlace) {
            // Consulta de permisos directamente
            $sql_permiso = "SELECT leer FROM permisos
                            WHERE id_rol = (SELECT id_rol FROM usuarios WHERE id_usuario = :id_usuario)
                            AND enlace_id = :id_enlace";
            $sentencia_permiso = $conexion->prepare($sql_permiso);
            $sentencia_permiso->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $sentencia_permiso->bindParam(':id_enlace', $id_enlace, PDO::PARAM_INT);
            $sentencia_permiso->execute();
    
            $permiso = $sentencia_permiso->fetch(PDO::FETCH_ASSOC);
    
            if ($permiso && $permiso['leer'] === 'visible') {
                return true;
            }
        }
        return false;
    }
    

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashBoard - Constantine</title>

    <link rel="stylesheet" href="../../css/header.css">
    <link rel="icon" href="../../../images/logos/logo_white_border.png" type="image/png">

    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

    <!--=============== Bootstrap CSS v5.3.2 ===============-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"/>
    <!--  Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.jqueryui.min.css">
</head>
<body>
    <!-- BARRA LATERAL  -->
    <div class="barra_lateral">
        <div class="logo_contenedor">
            <div class="nombre_pagina" id="logo">
                <img src="<?php echo $url_base; ?>images/logos/logo_white_border.png" alt="logo">
                <span>Constantine</span>
            </div>
            
        </div>
        <div class="linea"></div>

        <nav class="navegacion">
            <ul class="categorias">
                <li>
                    <a href="../../../index.php" class="categoria" target="_blank">
                        <i class="ri-earth-line icon line"></i>
                        <i class="ri-earth-fill icon fill"></i>
                        <span>Ver Sitio Web</span>
                    </a>
                </li>
                <li class="<?php echo ($directorioActual == 'inicio') ? 'active' : ''; ?>">
                    <a id="inbox" href="../inicio/" class="categoria">
                        <i class="ri-home-heart-line icon line"></i>
                        <i class="ri-home-heart-fill icon fill"></i> 
                        <span>Inicio</span>
                    </a>
                </li>

                <?php if (tiene_permiso_leer('banners')): ?>
                <li class="<?php echo ($directorioActual == 'banners') ? 'active' : ''; ?>">
                    <a href="../banners/" class="categoria">
                        <i class="ri-image-2-line icon line"></i>
                        <i class="ri-image-2-fill icon fill"></i>
                        <span>Banners</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('tienda')): ?>
                <li>
                    <a href="#" class="submenu-toggle categoria active">
                        <i class="ri-store-2-line icon line"></i>
                        <i class="ri-store-2-fill icon fill"></i>
                        <span>Tienda</span>
                        <i class="ri-arrow-right-s-line arrow"></i>
                    </a>
                    <ul class="submenu show">
                        <?php if (tiene_permiso_leer('publicos')): ?>
                        <li class="<?php echo ($directorioActual == 'publicos') ? 'active' : ''; ?>">
                            <a href="../publicos/" class="subcategoria">
                                <i class="ri-group-2-line icon line"></i>
                                <i class="ri-group-2-fill icon fill"></i>
                                <span>Categorías</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (tiene_permiso_leer('categorias')): ?>
                        <li class="<?php echo ($directorioActual == 'categorias') ? 'active' : ''; ?>">
                            <a href="../categorias/" class="subcategoria">
                                <i class="ri-price-tag-3-line icon line"></i>
                                <i class="ri-price-tag-3-fill icon fill"></i>
                                <span>SubCategorías</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (tiene_permiso_leer('productos')): ?>
                        <li class="<?php echo ($directorioActual == 'productos') ? 'active' : ''; ?>">
                            <a href="../productos/" class="subcategoria">
                                <i class="ri-shirt-line icon line"></i>
                                <i class="ri-shirt-fill icon fill"></i>
                                <span>Productos</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('info. negocio')): ?>
                <li>
                    <a class="submenu-toggle categoria active">
                        <i class="ri-information-line icon line" id="info"></i>
                        <i class="ri-information-fill icon fill"></i>
                        <span>Info. Negocio</span>
                        <i class="ri-arrow-right-s-line arrow"></i>
                    </a>
                    <ul class="submenu show">
                        <?php if (tiene_permiso_leer('contactos')): ?>
                        <li class="<?php echo ($directorioActual == 'contactos') ? 'active' : ''; ?>">
                            <a href="../contactos/#info" class="subcategoria">
                                <i class="ri-map-pin-line icon line"></i>
                                <i class="ri-map-pin-fill icon fill"></i>
                                <span>Contactos</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (tiene_permiso_leer('locales')): ?>
                        <li class="<?php echo ($directorioActual == 'locales') ? 'active' : ''; ?>">
                            <a href="../locales/#info" class="subcategoria">
                                <i class="ri-compass-line icon line"></i>
                                <i class="ri-compass-fill icon fill"></i>
                                <span>Locales</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('servicios')): ?>
                <li class="<?php echo ($directorioActual == 'servicios') ? 'active' : ''; ?>">
                    <a href="../servicios/#servicios" class="categoria">
                        <i class="ri-service-line icon line" id="servicios"></i>
                        <i class="ri-service-fill icon fill"></i>
                        <span>Servicios</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('usuarios')): ?>
                <li class="<?php echo ($directorioActual == 'usuarios') ? 'active' : ''; ?>">
                    <a href="../usuarios/#usuarios" class="categoria">
                        <i class="ri-id-card-line icon line" id="usuarios"></i>
                        <i class="ri-id-card-fill icon fill"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('clientes')): ?>
                <li class="<?php echo ($directorioActual == 'clientes') ? 'active' : ''; ?>">
                    <a href="../clientes/#clientes" class="categoria">
                        <i class="ri-user-line icon line" id="clientes"></i>
                        <i class="ri-user-fill icon fill"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('roles')): ?>
                <li class="<?php echo ($directorioActual == 'roles') ? 'active' : ''; ?>">
                    <a href="../roles/#roles" class="categoria">
                        <i class="ri-lock-line icon line" id="roles"></i>
                        <i class="ri-lock-fill icon fill"></i>
                        <span>Roles</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('blogs')): ?>
                <li class="<?php echo ($directorioActual == 'blogs') ? 'active' : ''; ?>">
                    <a href="../blogs/#blogs"   class="categoria">
                        <i class="ri-newspaper-line icon line" id="blogs"></i>
                        <i class="ri-newspaper-fill icon fill"></i>
                        <span>Blogs</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('pedidos')): ?>
                <li class="<?php echo ($directorioActual == 'pedidos') ? 'active' : ''; ?>">
                    <a href="../pedidos/#pedidos" class="categoria">
                        <i class="ri-shopping-cart-line icon line" id="pedidos"></i>
                        <i class="ri-shopping-cart-fill icon fill"></i>
                        <span>Pedidos</span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (tiene_permiso_leer('mensajes')): ?>
                <li class="<?php echo ($directorioActual == 'mensajes') ? 'active' : ''; ?>">
                    <a href="../mensajes/#mensajes" class="categoria">
                        <i class="ri-message-2-line icon line" id="mensajes"></i>
                        <i class="ri-message-2-fill icon fill"></i>
                        <span>Mensajes</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <div>
            <div class="linea"></div>

            <div class="modo_oscuro">
                <div class="info">
                    <i class="ri-star-half-line"></i>
                    <span>Modo Oscuro</span>
                </div>
                <div class="switch">
                    <div class="base">
                        <div class="circulo"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- BARRA SUPERIOR -->
    <div class="barra_superior">
        <div class="informacion_barra">
            <div class="fecha">
                <?php include "fecha_admin.php"; ?> <i class="ri-flashlight-fill"></i>
            </div>

            <!-- Contenedor de usuario con imagen, nombre completo y género -->
            <div class="usuario" id="usuarioMenu">
                <input type="checkbox" id="toggleProfileMenu" class="toggle-profile_menu">
                <div class="info_usuario">
                    <div class="mensaje_usuario">
                        <span class="nombre">Hola, <?php echo $_SESSION['nombres']; ?></span>
                    </div>
                    <?php
                        $rutaImagenUsuario = "../../../images/usuarios/" . $_SESSION['foto'];
                        $rutaImagenCliente = "../../../images/clientes/" . $_SESSION['foto'];
                        $rutaImagenDefault = "../../../images/default/usuario_default.png";

                        if (file_exists($rutaImagenUsuario) && !empty($_SESSION['foto'])) {
                            $rutaImagen = $rutaImagenUsuario;
                        } elseif (file_exists($rutaImagenCliente) && !empty($_SESSION['foto'])) {
                            $rutaImagen = $rutaImagenCliente;
                        } else {
                            $rutaImagen = $rutaImagenDefault;
                        }
                    ?>
                    <div>
                        <img src="<?php echo $rutaImagen; ?>" alt="Foto de usuario">
                        <label class="boton_usuario" for="toggleProfileMenu">
                            <i class="ri-more-fill"></i>
                        </label>
                    </div>
                </div>

                <!-- Módulo de opciones del usuario -->
                <div class="profile_menu">
                    <!-- Mostrar la imagen, nombre completo y género al inicio del menú de perfil -->
                    <div class="profile_header">
                        <img src="<?php echo $rutaImagen; ?>" alt="Foto de usuario" class="profile_header-img">
                        <div class="profile_header-info">
                            <span class="nombre_completo"><?php echo $_SESSION['nombres']; ?></span>
                            <span class="nombre_completo"><?php echo $_SESSION['apellidos']; ?></span>
                            <span class="email"><?php echo $_SESSION['rol']; ?></span>
                        </div>
                    </div>

                    <a href="#" class="profile_menu-item" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                        <i class="ri-edit-line"></i> Editar perfil
                    </a>
                    </a>
                    <a href="../inicio/cerrar.php" class="profile_menu-item">
                        <i class="ri-shut-down-line"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        </div>

        <div class="botones_barra_superior">
            <a class="boton" href="../inicio/cerrar.php">
                <i class="ri-shut-down-line"></i>
                <span>Cerrar Sesión</span>
            </a>

            <!-- Menú hamburguesa -->
            <div class="menu_hamburguesa">
                <i class="ri-menu-fill icono mostrar" id="iconoAbrir"></i>
                <i class="ri-close-large-fill icono" id="iconoCerrar"></i>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR PERFIL -->
    <div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalEditarPerfilLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                    <!-- Formulario de edición -->
                    <form action="../../templates/editar_perfil.php" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Imagen y botones -->
                            <div class="col-12 col-md-4">
                                <div class="profile-photo">
                                    <div class="photo-wrapper">
                                        <!-- Foto de perfil previsualizada -->
                                        <img id="preview" src="<?php echo isset($_SESSION['foto']) && !empty($_SESSION['foto']) ? '../../../images/usuarios/' . $_SESSION['foto'] : '../../../images/usuarios/usuario_default.png'; ?>" alt="Foto de Perfil">
                                    </div>
                                    <div class="photo-buttons">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('foto').click();">
                                            <i class="ri-upload-line"></i> Subir
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" id="btnEliminar">
                                            <i class="ri-delete-bin-line"></i> Eliminar
                                        </button>
                                    </div>
                                    <input type="file" class="form-control d-none" id="foto" name="foto" accept="image/*">
                                </div>
                            </div>
                            <!-- Inputs a la derecha (2 columnas en modo escritorio) -->
                            <div class="col-12 col-md-8">
                                <div class="row">
                                    <!-- Primera columna de inputs -->
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="dni">DNI</label>
                                            <input type="text" class="form-control" id="dni" name="dni" placeholder="DNI" value="<?php echo $_SESSION['dni']; ?>" pattern="\d{8}" title="Debe contener 8 dígitos numéricos">
                                        </div>
                                        <div class="form-group">
                                            <label for="nombre">Nombre Completo</label>
                                            <div class="input-wrapper full-name-wrapper">
                                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo $_SESSION['nombres']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="correo">Correo Electrónico</label>
                                            <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" value="<?php echo $_SESSION['correo']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="password">Contraseña</label>
                                            <input type="password" class="form-control" name="password" id="password" placeholder="Dejar vacío para no cambiar">
                                        </div>
                                    </div>

                                    <!-- Segunda columna de inputs -->
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" value="<?php echo $_SESSION['direccion']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="genero">Género</label>
                                            <select class="form-control" id="genero" name="genero" required>
                                                <option value="Masculino" <?php echo $_SESSION['genero'] == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                                <option value="Femenino" <?php echo $_SESSION['genero'] == 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                                <option value="Otro" <?php echo $_SESSION['genero'] == 'Otro' ? 'selected' : ''; ?>>Otro</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" value="<?php echo $_SESSION['telefono']; ?>" pattern="\d{9,20}" title="Debe contener entre 9 y 20 dígitos numéricos" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirmar Contraseña">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Guardar Cambios</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CONFIRMAR ELIMINACIÓN DE REGISTRO -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                    <i class="ri-delete-bin-2-fill"></i> Confirmar eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <i class="ri-alert-fill text-warning"></i> 
                    ¿Estás seguro de que deseas eliminar este registro? <br>
                    Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Cancelar
                    </button>
                    <a id="confirmDeleteButton" href="#" class="btn btn-danger d-flex align-items-center">
                    <i class="ri-delete-bin-2-fill me-1"></i> Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>
    <style>
        .disabled {
            pointer-events: none;
            color: gray;
            text-decoration: none;
            cursor: not-allowed;
        }

        .hidden{
            display: none !important;
        }
    </style>
    <main>
        