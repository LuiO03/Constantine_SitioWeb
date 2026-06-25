<?php
include("../inicio/conexion.php");

// ============== CONSULTA TABLA PRODUCTOS CON VARIANTES ============== //
$sentencia = $conexion->prepare("
    SELECT 
        p.*, 
        c.nombre_categoria, 
        pub.nombre_publico, 
        v.id_variante, 
        col.nombre_color, 
        t.nombre_talla, 
        v.stock AS stock_variante
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    LEFT JOIN publico pub ON p.id_publico = pub.id_publico
    LEFT JOIN productos_variantes v ON p.id_producto = v.id_producto
    LEFT JOIN colores col ON v.id_color = col.id_color
    LEFT JOIN tallas t ON v.id_talla = t.id_talla
");
$sentencia->execute();
$datosProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Agrupar productos y variantes
$listaProductos = [];
foreach ($datosProductos as $fila) {
    $idProducto = $fila['id_producto'];

    if (!isset($listaProductos[$idProducto])) {
        // Agregar datos del producto si aún no existe
        $listaProductos[$idProducto] = [
            'id_producto' => $fila['id_producto'],
            'codigo_producto' => htmlspecialchars($fila['codigo_producto'], ENT_QUOTES, 'UTF-8'),
            'nombre_producto' => htmlspecialchars($fila['nombre_producto'], ENT_QUOTES, 'UTF-8'),
            'descripcion' => htmlspecialchars($fila['descripcion'], ENT_QUOTES, 'UTF-8'),
            'imagen' => htmlspecialchars($fila['imagen'], ENT_QUOTES, 'UTF-8'),
            'precio_venta' => $fila['precio_venta'],
            'stock_total' => $fila['stock_total'],
            'nombre_categoria' => htmlspecialchars($fila['nombre_categoria'], ENT_QUOTES, 'UTF-8'),
            'nombre_publico' => htmlspecialchars($fila['nombre_publico'], ENT_QUOTES, 'UTF-8'),
            'fecha_creacion' => $fila['fecha_creacion'],
            'fecha_actualizacion' => $fila['fecha_actualizacion'],
            'variantes' => [] // Inicia con un array vacío para las variantes
        ];
    }

    // Agregar variantes si existen
    if (!empty($fila['id_variante'])) {
        $listaProductos[$idProducto]['variantes'][] = [
            'id_variante' => $fila['id_variante'],
            'color' => htmlspecialchars($fila['nombre_color'], ENT_QUOTES, 'UTF-8'),
            'talla' => htmlspecialchars($fila['nombre_talla'], ENT_QUOTES, 'UTF-8'),
            'stock' => $fila['stock_variante']
        ];
    }
}

// Convertir en un array indexado
$listaProductos = array_values($listaProductos);

include("../../templates/header_admin.php");
?>


<!-- LISTA DE PRODUCTOS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm  d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-shirt-fill"></i>
            Administrar <span class="text_red">Productos</span>
        </span>
        <div class="d-flex gap-2">
            <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
                <i class="ri-arrow-left-s-line"></i> Página Principal
            </a>
            <a name="sitio_web" id="sitio_web" class="btn btn-danger d-flex align-items-center gap-1" href="../../../index.php" target="_blank" role="button">
                <i class="ri-earth-line"></i> Revisar Cambios
            </a>
        </div>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="table-responsive">

            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped display nowrap" cellspacing="0">
                <thead>
                    <tr>
                        <th class="w-10 text-center">ID</th>
                        <th class="w-15 text-center">Código</th>
                        <th class="w-20 text-center">Nombre</th>
                        <th class="w-15 text-center">Categoría</th>
                        <th class="w-15 text-center">SubCategoría</th>
                        <th class="w-10 text-center">Stock T.</th>
                        <th class="w-10 text-center">P. Venta</th>
                        <th class="w-20 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaProductos as $producto) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $producto['id_producto']; ?></td>
                            <td class="text-center"><?php echo $producto['codigo_producto']; ?></td>
                            <td><?php echo $producto['nombre_producto']; ?></td>
                            <td class="text-center"><?php echo $producto['nombre_publico']; ?></td>
                            <td class="text-center"><?php echo $producto['nombre_categoria']; ?></td>
                            <td class="text-center"><?php echo $producto['stock_total']; ?></td>
                            <td class="text-center">S/ <?php echo $producto['precio_venta']; ?></td>
                            <td class="align-middle text-center">

                                <!-- Botón Ver Producto -->
                                <button enabled class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerProducto<?php echo $producto['id_producto']; ?>">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <a class="btn btn-warning btn-sm <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $producto['id_producto']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button class="btn btn-danger btn-sm <?php echo $permiso['eliminar']; ?>" onclick="confirmDelete(<?php echo $producto['id_producto']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                                <a class="btn btn-success btn-sm <?php echo $permiso['editar']; ?>" href="variantes.php?txtID=<?php echo $producto['id_producto']; ?>" role="button">
                                    <i class="ri-list-unordered"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
           
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus productos de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE PRODUCTOS PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-shirt-fill"></i>
            Administrar <span class="text_red">Productos</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaProductos as $producto) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $producto['id_producto']; ?><br>
                                <strong><i class="ri-barcode-box-line"></i> Código:</strong> <?php echo $producto['codigo_producto']; ?><br>
                                <strong><i class="ri-t-shirt-2-line"></i> Nombre:</strong> <?php echo $producto['nombre_producto']; ?><br>
                                <strong><i class="ri-price-tag-3-line"></i> Categoría:</strong> <?php echo $producto['nombre_categoria']; ?><br>
                                <strong><i class="ri-group-line"></i> Público:</strong> <?php echo $producto['nombre_publico']; ?><br>
                                <strong><i class="ri-drop-line"></i> Stock Total:</strong> <?php echo $producto['stock_total']; ?><br>
                                <strong><i class="ri-money-dollar-circle-line"></i> Precio Venta:</strong> <?php echo $producto['precio_venta']; ?><br>
                                <strong><i class="ri-calendar-line"></i> Actualizado:</strong> <?php echo $producto['fecha_actualizacion']; ?><br>
                                <strong><i class="ri-calendar-check-line"></i> Creación:</strong> <?php echo $producto['fecha_creacion']; ?><br>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $producto['id_producto']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button class="btn btn-danger btn-sm mx-1 <?php echo $permiso['eliminar']; ?>" onclick="confirmDelete(<?php echo $producto['id_producto']; ?>)" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus productos de forma eficiente</small>
    </div>
</div>

<!-- MODAL DE PRODUCTOS -->
<?php foreach($listaProductos as $producto) { ?>
    <!-- Modal para mostrar los detalles del producto -->
    <div class="modal fade" id="modalVerProducto<?php echo $producto['id_producto']; ?>" tabindex="-1" aria-labelledby="modalVerProductoLabel<?php echo $producto['id_producto']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Ajustes responsivos con modal más grande -->
            <div class="modal-content rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <div class="modal-header bg-primary text-white rounded-0">
                    <h5 class="modal-title" id="modalVerProductoLabel<?php echo $producto['id_producto']; ?>">Detalles del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Imagen del producto -->
                        <div class="col-md-5 text-center">
                            <?php 
                                $rutaImagen = "../../../images/productos/". $producto['nombre_publico']."/". $producto['imagen'];
                                if (!file_exists($rutaImagen) || empty($producto['imagen'])) {
                                    $rutaImagen = "../../../images/default/producto_default.png";
                                }
                            ?>
                            <img src="<?php echo $rutaImagen; ?>" alt="Imagen del Producto" class="img-fluid rounded mb-3" style="max-width: 100%; border-radius: 10px;">
                        </div>
                        <!-- Información del producto -->
                        <div class="col-md-7">
                            <div class="row mb-3">
                                <div class="col-4"><strong>Código</strong></div>
                                <div class="col-8"><?php echo $producto['codigo_producto']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Nombre</strong></div>
                                <div class="col-8"><?php echo $producto['nombre_producto']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Categoría</strong></div>
                                <div class="col-8"><?php echo $producto['nombre_categoria']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Público</strong></div>
                                <div class="col-8"><?php echo $producto['nombre_publico']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Precio de Venta</strong></div>
                                <div class="col-8">S/ <?php echo $producto['precio_venta']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Stock Total</strong></div>
                                <div class="col-8"><?php echo $producto['stock_total']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Descripción</strong></div>
                                <div class="col-8"><?php echo $producto['descripcion'] ?: 'Sin descripción'; ?></div>
                            </div>
                        </div>
                    </div>
                    <!-- Variantes del producto -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6><strong>Variantes</strong></h6>
                            <?php if (!empty($producto['variantes'])) { ?>
                                <table class="table table-sm table-striped table-bordered no-datatable">
                                    <thead>
                                        <tr>
                                            <th>Color</th>
                                            <th>Talla</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($producto['variantes'] as $variante) { ?>
                                            <tr>
                                                <td><?php echo $variante['color']; ?></td>
                                                <td><?php echo $variante['talla']; ?></td>
                                                <td><?php echo $variante['stock']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p>No hay variantes disponibles para este producto.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<!-- BOTONES FLOTANTES EN LA PANTALLA -->
<div class="main_botones_flotantes">
    <div class="botones_fixed">
        <li>
            <a class="boton_pdf" href="generar-pdf.php">
                <i class="ri-file-pdf-fill"></i>
            </a>
        </li>
        <li>
            <a class="boton_excel" href="generar-excel.php">
                <i class="ri-file-excel-fill"></i>
            </a>
        </li>
        <li>
            <a class="boton_agregar <?php echo $permiso['crear']; ?>" href="crear.php">
                <i class="ri-add-line"></i>
            </a>
        </li>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
