<?php 
    // Conexión a la base de datos
    include("../admin/seccion/inicio/conexion.php");

    // Obtener lista de públicos
    $sentenciaPublico = $conexion->prepare("SELECT * FROM publico");
    $sentenciaPublico->execute();
    $lista_publicos = $sentenciaPublico->fetchAll(PDO::FETCH_ASSOC);

    // Obtener valores de público y categoría seleccionados de los parámetros GET
    $publicoSeleccionado = isset($_GET['publico']) ? $_GET['publico'] : '';
    $categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';
    $ordenSeleccionado = isset($_GET['orden']) ? $_GET['orden'] : '';

    // Obtener categorías relacionadas con el público seleccionado
    if (!empty($publicoSeleccionado)) {
        $stmtCategorias = $conexion->prepare("SELECT c.* FROM categorias c 
                                            INNER JOIN productos p ON p.id_categoria = c.id_categoria
                                            WHERE p.id_publico = :id_publico
                                            GROUP BY c.id_categoria");
        $stmtCategorias->bindParam(':id_publico', $publicoSeleccionado, PDO::PARAM_INT);
        $stmtCategorias->execute();
        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Obtener todas las categorías si no hay público seleccionado
        $stmtCategorias = $conexion->prepare("SELECT * FROM categorias");
        $stmtCategorias->execute();
        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
    }

    // Construir la consulta de productos con filtros dinámicos de público, categoría y orden
    $queryProductos = "SELECT p.*, c.nombre_categoria, pub.nombre_publico 
                    FROM productos p
                    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                    INNER JOIN publico pub ON p.id_publico = pub.id_publico";

    // Aplicar filtros de público y categoría si están seleccionados
    $conditions = [];
    $params = [];

    if (!empty($publicoSeleccionado)) {
        $conditions[] = "p.id_publico = :id_publico";
        $params[':id_publico'] = $publicoSeleccionado;
    }

    if (!empty($categoriaSeleccionada)) {
        $conditions[] = "c.nombre_categoria = :categoria";
        $params[':categoria'] = $categoriaSeleccionada;
    }

    // Añadir condiciones a la consulta
    if (count($conditions) > 0) {
        $queryProductos .= " WHERE " . implode(" AND ", $conditions);
    }

    // Añadir ordenamiento según la selección de orden
    switch ($ordenSeleccionado) {
        case 'az':
            $queryProductos .= " ORDER BY p.nombre_producto ASC";
            break;
        case 'za':
            $queryProductos .= " ORDER BY p.nombre_producto DESC";
            break;
        case 'mayor_precio':
            $queryProductos .= " ORDER BY p.precio_venta DESC";
            break;
        case 'menor_precio':
            $queryProductos .= " ORDER BY p.precio_venta ASC";
            break;
        case 'nuevo':
            $queryProductos .= " ORDER BY p.fecha_creacion DESC";
            break;
        default:
            $queryProductos .= " ORDER BY p.nombre_producto ASC"; // Orden predeterminado
            break;
    }

    // Preparar y ejecutar consulta de productos
    $stmtProductos = $conexion->prepare($queryProductos);
    foreach ($params as $key => $value) {
        $stmtProductos->bindValue($key, $value);
    }
    $stmtProductos->execute();
    $productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);

    // Consultar banners activos para la página de productos
    $sentencia = $conexion->prepare("SELECT * FROM banners WHERE pagina = 'productos' AND estado = 1;");
    $sentencia->execute();
    $lista_banners = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    $cantidadProductos = count($productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constantine - Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/nav.css">
    <link rel="stylesheet" href="../style/base.css">
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="producto.css">
    <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
</head>
<body>
    <!-- Header -->
    <?php include("../layout/header.php"); ?>

    <main>

        <?php foreach ($lista_banners as $banner) { ?>
            <section class="banner_contenido" style="background-image: url('../images/banners/<?php echo htmlspecialchars($banner['imagen']); ?>');">
                <div class="banner_categoria">
                    <?php if (!empty($categoriaSeleccionada) && !empty($publicoSeleccionado)) { ?>
                        <div class="enfasis_banner">
                            <?php echo htmlspecialchars($categoriaSeleccionada); ?>
                        </div>
                        <div class="titulo_banner"><?php echo htmlspecialchars($productos[0]['nombre_publico']); ?></div>
                    <?php } elseif (!empty($categoriaSeleccionada)) { ?>
                        <div class="enfasis_banner">
                            <?php echo htmlspecialchars($categoriaSeleccionada); ?>
                        </div>
                    <?php } elseif (!empty($publicoSeleccionado)) { ?>
                        <div class="titulo_banner"><?php echo htmlspecialchars($productos[0]['nombre_publico']); ?></div>
                    <?php } else { ?>
                        <div class="enfasis_banner">Nuestras</div>
                        <div class="titulo_banner">Colecciones</div>
                    <?php } ?>
                </div>
            </section>
        <?php } ?>

        <div class="container">
            <!-- Contenedor principal -->
            <aside class="filtros_categorias">
                <div class="titulo_lista">CATEGORÍAS</div>
                <ul>
                <?php foreach ($categorias as $categoria) { ?>
                    <li class="<?php echo $categoriaSeleccionada == $categoria['nombre_categoria'] ? 'activo' : ''; ?>">
                        <a href="?publico=<?php echo urlencode($publicoSeleccionado); ?>&categoria=<?php echo urlencode($categoria['nombre_categoria']); ?>">
                            <i class="ri-arrow-right-s-line"></i> <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                        </a>
                    </li>
                    <div class="linea"></div>
                <?php } ?>
                </ul>
            </aside>

            <section class="productos-section">
                <div class="productos_encabezado">
                    <span class="titulo_lista">PRODUCTOS (<?php echo $cantidadProductos; ?>)</span>
                    <div class="ordenar-productos">
                        <select id="orden" onchange="ordenarProductos()">
                            <option value="">Ordenar Por</option>
                            <option value="az" <?php echo $ordenSeleccionado == 'az' ? 'selected' : ''; ?>>A - Z</option>
                            <option value="za" <?php echo $ordenSeleccionado == 'za' ? 'selected' : ''; ?>>Z - A</option>
                            <option value="mayor_precio" <?php echo $ordenSeleccionado == 'mayor_precio' ? 'selected' : ''; ?>>Mayor precio</option>
                            <option value="menor_precio" <?php echo $ordenSeleccionado == 'menor_precio' ? 'selected' : ''; ?>>Menor precio</option>
                            <option value="nuevo" <?php echo $ordenSeleccionado == 'nuevo' ? 'selected' : ''; ?>>Lo más nuevo</option>
                        </select>
                    </div>
                </div>
                <script>
                    // Función para redirigir con el parámetro de orden
                    function ordenarProductos() {
                        const orden = document.getElementById('orden').value;
                        const publico = "<?php echo urlencode($publicoSeleccionado); ?>";
                        const categoria = "<?php echo urlencode($categoriaSeleccionada); ?>";
                        window.location.href = `?publico=${publico}&categoria=${categoria}&orden=${orden}`;
                    }
                </script>
                <div class="productos">
                    <?php if (count($productos) > 0) {
                        foreach ($productos as $producto) { ?>
                            <div class="producto">
                                <div class="imagen_contendedor">

                                    <img src="<?php echo $url_base; ?>images/productos/<?php echo strtolower(htmlspecialchars($producto['nombre_publico'])); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                                    <a class="ver_producto" href="<?php echo $url_base; ?>productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                        <i class="ri-eye-fill"></i>
                                        <span>VER</span>
                                    </a>
                                </div>

                                <div class="producto_info">
                                    <h3>
                                        Constantine <?php echo htmlspecialchars($producto['nombre_publico']); ?>
                                    </h3>
                                    <p>
                                        <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                                    </p>
                                    <div class="precio_info">
                                        <span class="precio">
                                            S/.<?php echo number_format($producto['precio_venta'], 2); ?>
                                        </span>
                                        <div class="producto_botones">
                                            
                                            <a class="btn_agregar" href="<?php echo $url_base; ?>productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                                <i class="ri-heart-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <p class="no-productos">No hay productos disponibles.</p>
                    <?php } ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <?php include("../layout/footer.php"); ?>
</body>
</html>
