<?php
    if ($_SERVER['HTTP_HOST'] === 'localhost') {
        // URL local XAMPP
        $url_base = "http://localhost/Constantine_SitioWeb/";
    } else {
        // URL producción InfinityFree
        $url_base = "https://luiosorio.lovestoblog.com/Constantine_SitioWeb/";
    }
    // Aquí suponemos que 'nombres' es la clave que indica si el usuario está logueado
    $usuario_logeado = isset($_SESSION['nombres']) && !empty($_SESSION['nombres']);

    $id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : "";

    // --=============== BARRA LATERAL DEL CARRITO ===============-->

    // Consulta para obtener los productos del carrito del usuario actual
    $query = $conexion->prepare("
        SELECT c.id_carrito, c.cantidad, c.precio_unitario, c.precio_total,
            p.nombre_producto, p.imagen,
            col.nombre_color, col.codigo_color,
            t.nombre_talla,
            pub.nombre_publico
        FROM carrito c
        INNER JOIN productos_variantes pv ON c.id_variante = pv.id_variante
        INNER JOIN productos p ON pv.id_producto = p.id_producto
        INNER JOIN colores col ON pv.id_color = col.id_color
        INNER JOIN tallas t ON pv.id_talla = t.id_talla
        INNER JOIN publico pub ON p.id_publico = pub.id_publico
        WHERE c.id_usuario = :id_usuario
    ");
    $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $query->execute();
    $carrito = $query->fetchAll(PDO::FETCH_ASSOC);

    // Redes Sociales
    $sentencia = $conexion->prepare("SELECT * FROM redes_sociales WHERE estado = 1");
    $sentencia->execute();
    $redes_sociales = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Contactos del Negocio
    $sentencia = $conexion->prepare("SELECT * FROM contactos_negocio");
    $sentencia->execute();
    $contactos_negocio = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Categorías junto con públicos
    $sentencia = $conexion->prepare("
        SELECT pu.id_publico, pu.nombre_publico, c.nombre_categoria, c.id_categoria
        FROM categorias c
        JOIN productos p ON c.id_categoria = p.id_categoria
        JOIN publico pu ON p.id_publico = pu.id_publico
        GROUP BY pu.id_publico, c.id_categoria
    ");
    $sentencia->execute();
    $categorias_publicos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar categorías por público
    $publicos_categorias = [];
    foreach ($categorias_publicos as $fila) {
        $publicos_categorias[$fila['id_publico']]['nombre_publico'] = $fila['nombre_publico'];
        $publicos_categorias[$fila['id_publico']]['categorias'][] = $fila;
    }

    // Total de productos en el carrito
    $sentencia = $conexion->prepare("SELECT SUM(cantidad) AS total_productos FROM carrito WHERE id_usuario = :id_usuario");
    $sentencia->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $sentencia->execute();
    $total_productos = $sentencia->fetch(PDO::FETCH_ASSOC)['total_productos'] ?? 0;
?>

        <!--=============== PRELOADER ===============-->
        <!--=
        <div id="preloader">
            <img src="<?php //echo $url_base;?>/images/logos/logo_solo_white.png" class="animacion_logo">
            <div class="barra_carga">
                <div class="progreso"></div>
                <h1>Cargando...</h1>
            </div>
        </div>
        -->
        <div class="nav__paginas">
            <ul>
                <!--=============== NOSOTROS ===============-->
                <li><a href="<?php echo $url_base;?>/nosotros/" class="pag__link">Nosotros</a></li>
                    
                <!--=============== SERVICIOS ===============-->
                <li><a href="<?php echo $url_base;?>/servicios/" class="pag__link">Servicios</a></li>

                <!--=============== BLOG ===============-->
                <li><a href="<?php echo $url_base;?>/blog/" class="pag__link">Blog</a></li>

                <!--=============== CONTACTOS ===============-->
                <li><a href="<?php echo $url_base;?>/contactos/" class="pag__link">Contactos</a></li>
            </ul>
        </div>
        
        <!--=============== HEADER ===============-->
        <header class="header_nav">
            
            <nav class="nav">
                <div class="nav__data">
                    <span class="espacio"></span>
                    <!-- Logo -->
                    <a href="<?php echo $url_base;?>/index.php" class="nav__logo">
                        <img src="<?php echo $url_base;?>images\logos\logo_solo_black.png" alt="Logo Constantine">
                        <div class="texto_logo">
                            <span class="tienda_texto">Tiendas</span>
                            <div class="logo_texto">
                                CONSTANTINE
                            </div>
                        </div>
                        
                    </a>
                    <div class="nav_data_botones">

                    <div class="nav__user">
                        <?php if ($usuario_logeado): ?>
                            <!-- Mostrar la clase 'usuario' si el usuario está logueado -->
                            <div class="usuario" id="usuarioMenu">
                                <input type="checkbox" id="toggleProfileMenu" class="toggle-profile_menu">
                                <div class="info_usuario">
                                    <div class="mensaje_usuario">
                                        <i class="ri-map-pin-user-fill"></i>
                                        <span class="nombre_completo"><?php echo $_SESSION['nombres']; ?></span>
                                    </div>
                                    <div>
                                        <label class="boton_usuario" for="toggleProfileMenu">
                                            <i class="ri-arrow-down-s-fill"></i>
                                        </label>
                                    </div>
                                </div>

                                <div class="profile_menu">
                                    <div class="profile_header">
                                        <div class="profile_header-info">
                                            <span class="nombre_completo"><?php echo $_SESSION['nombres']; ?></span>
                                            <span class="nombre_completo"><?php echo $_SESSION['apellidos']; ?></span>
                                        </div>
                                    </div>
                                    
                                    <a href="<?php echo $url_base; ?>/admin/seccion/inicio/index.php" class="profile_menu-item">
                                        <i class="ri-edit-line"></i> Mi Perfil
                                    </a>
                                    <a href="<?php echo $url_base; ?>/carrito/confirmacion.php?pedido_id=<?php echo $pedidoPendiente['id_pedido']; ?>" class="profile_menu-item">
                                        <i class="ri-shopping-cart-line"></i> Mis Pedidos
                                    </a>
                                    <a href="<?php echo $url_base; ?>/admin/seccion/inicio/cerrar.php" class="profile_menu-item">
                                        <i class="ri-shut-down-line"></i> Cerrar sesión
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Mostrar la clase 'user__boton' si el usuario no está logueado -->
                            <a class="user__boton" href="<?php echo $url_base; ?>/admin/seccion/inicio/index.php">
                                <i class="ri-user-smile-fill"></i>
                                <span class="btn_txt">Iniciar Sesión</span>
                            </a>
                        <?php endif; ?>
                    </div>
                        <!-- Icono de Menú Responsive -->
                        <div class="nav__toggle" id="nav-toggle">
                            <i class="ri-menu-line nav__burger"></i>
                            <i class="ri-close-line nav__close"></i>
                        </div>
                    </div>
                </div>
                <div class="linea"></div>
                <!--=============== NAV MENU ===============-->
                <div class="nav__menu" id="nav-menu">
                    <ul class="nav__list">

                        <a class="buscador_boton" href="#">
                            <i class="ri-search-line"></i>
                        </a>

                        <ul class="nav__publico">
                            
                        <?php foreach ($publicos_categorias as $id_publico => $publico): ?>
                            <li class="dropdown__item">
                                <a href="<?php echo $url_base;?>/productos/?publico=<?php echo $id_publico; ?>" class="nav__link">
                                    <?php echo htmlspecialchars($publico['nombre_publico']); ?> <i class="ri-arrow-down-s-line dropdown__arrow"></i>
                                </a>
                                <ul class="dropdown__menu">
                                    <?php foreach ($publico['categorias'] as $categoria): ?>
                                        <li>
                                            <a href="<?php echo $url_base;?>/productos/?publico=<?php echo $id_publico; ?>&categoria=<?php echo $categoria['nombre_categoria']; ?>" class="dropdown__link">

                                                <i class="ri-shopping-bag-line"></i> <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                            <li>
                                <a href="<?php echo $url_base;?>/productos/?publico=" class="nav__link">
                                    Ver todo
                                </a>
                            </li>
                        </ul>

                        <!-- Botón Login y Carrito -->
                        <div class="nav__user ">

                            <a class="nav_boton carrito_boton" >
                                <i class="ri-shopping-bag-2-line"></i>
                                <?php if ($total_productos > 0): ?>
                                    <div class="contador-carrito"><?php echo $total_productos; ?></div>
                                <?php endif; ?>
                                <span>Carrito de Compras</span>
                            </a>

                            <a class="nav_boton deseos_boton" href="#carrito">
                                <i class="ri-heart-3-line"></i>
                                <span class="">Lista de Deseos</span>
                            </a>
                        </div>

                        <!--=============== BARRA LATERAL DEL CARRITO ===============-->
                        <div class="overlay" id="overlay"></div>
                        <div class="sidebar-carrito" id="sidebarCarrito">
                            <div class="sidebar-header">
                                <h2>Tu carrito</h2>
                                <button class="btn-cerrar" id="cerrarSidebar">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                            <div class="sidebar-body">
                                <?php if (!empty($carrito)) : ?>
                                    <ul class="lista-productos">
                                        <?php foreach ($carrito as $item) : ?>
                                            <li class="producto-item">
                                                <img src="<?php echo $url_base;?>/images/productos/<?php echo htmlspecialchars($item['nombre_publico']); ?>/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>" class="producto-imagen">
                                                <div class="producto-info">
                                                    <h4><?php echo htmlspecialchars($item['nombre_producto']); ?></h4>
                                                    <p>Color: <?php echo htmlspecialchars($item['nombre_color']); ?></p>
                                                    <p>Talla: <?php echo htmlspecialchars($item['nombre_talla']); ?></p>
                                                    <p>Cantidad: <?php echo $item['cantidad']; ?> x S/.<?php echo number_format($item['precio_unitario'], 2); ?></p>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p class="mensaje">Tu carrito está vacío.</p>
                                <?php endif; ?>
                            </div>
                            <div class="sidebar-footer">
                                <p class="total">
                                    Total: S/. <span id="totalCarrito"><?php echo number_format(array_sum(array_column($carrito, 'precio_total')), 2); ?></span>
                                </p>
                                <div class="botones">
                                    <a href="<?php echo $url_base; ?>/carrito/carrito.php" class="btn-general btn-ver">
                                        <i class="ri-eye-line"></i> Ver Carrito
                                    </a>
                                    <button class="btn-general btn-procesar">
                                        <i class="ri-bank-card-fill"></i> Finalizar Pedido
                                    </button>
                                </div>
                            </div>
                        </div>

                        <ul class="nav_info_movil">
                            <div class="linea"></div>
                            <!--=============== NOSOTROS ===============-->
                            <li><a href="<?php echo $url_base;?>/nosotros/" class="nav__link">Nosotros<?php echo $total_productos; ?></a></li>
                            
                            <!--=============== SERVICIOS ===============-->
                            <li><a href="<?php echo $url_base;?>/servicios/" class="nav__link">Servicios</a></li>
    
                            <!--=============== BLOG ===============-->
                            <li><a href="<?php echo $url_base;?>/blog/" class="nav__link">Blog</a></li>
    
                            <!--=============== CONTACTOS ===============-->
                            <li><a href="<?php echo $url_base;?>/contactos/" class="nav__link">Contactos</a></li>
                            
                        </ul>

                    </ul>
                </div>
            </nav>
        </header>