<?php
include("../inicio/conexion.php");

// Obtener el ID del público desde la URL
$publicoID = isset($_GET['txtID']) ? $_GET['txtID'] : 0;

// ============== CONSULTA DE PRODUCTOS POR PÚBLICO ============== //
$sentencia = $conexion->prepare("
    SELECT 
        p.*, 
        c.nombre_categoria, 
        pub.nombre_publico
    FROM productos p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    LEFT JOIN publico pub ON p.id_publico = pub.id_publico
    WHERE p.id_publico = :publicoID
");
$sentencia->bindParam(':publicoID', $publicoID, PDO::PARAM_INT);
$sentencia->execute();
$datosProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Agrupar productos por público
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
            'fecha_actualizacion' => $fila['fecha_actualizacion']
        ];
    }
}

// Convertir en un array indexado
$listaProductos = array_values($listaProductos);

include("../../templates/header_admin.php");
?>

<!-- INTERFACE PARA PRODUCTOS POR PÚBLICO -->

<div class="card shadow-sm rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-group-line"></i>
            Productos para el Público: <span class="text_red"><?php echo htmlspecialchars($listaProductos[0]['nombre_publico'], ENT_QUOTES, 'UTF-8'); ?></span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="publico.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Volver a Públicos
        </a>
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
                            <td class="text-center"><?php echo $producto['nombre_categoria']; ?></td>
                            <td class="text-center"><?php echo $producto['stock_total']; ?></td>
                            <td class="text-center">S/ <?php echo $producto['precio_venta']; ?></td>
                            <td class="align-middle text-center">
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerProducto<?php echo $producto['id_producto']; ?>">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <a class="btn btn-warning btn-sm" href="editar.php?txtID=<?php echo $producto['id_producto']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $producto['id_producto']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                                <a class="btn btn-success btn-sm" href="../productos/variantes.php?txtID=<?php echo $producto['id_producto']; ?>" role="button">
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
        <small>Gestiona los productos para el público: <?php echo htmlspecialchars($listaProductos[0]['nombre_publico'], ENT_QUOTES, 'UTF-8'); ?></small>
    </div>
</div>

<!-- MODAL DE PRODUCTOS -->
<?php foreach($listaProductos as $producto) { ?>
    <div class="modal fade" id="modalVerProducto<?php echo $producto['id_producto']; ?>" tabindex="-1" aria-labelledby="modalVerProductoLabel<?php echo $producto['id_producto']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <div class="modal-header bg-primary text-white rounded-0">
                    <h5 class="modal-title" id="modalVerProductoLabel<?php echo $producto['id_producto']; ?>">Detalles del Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 text-center">
                            <?php 
                                $rutaImagen = "../../../images/productos/". $producto['nombre_publico']."/". $producto['imagen'];
                                if (!file_exists($rutaImagen) || empty($producto['imagen'])) {
                                    $rutaImagen = "../../../images/default/producto_default.png";
                                }
                            ?>
                            <img src="<?php echo $rutaImagen; ?>" alt="Imagen del Producto" class="img-fluid rounded mb-3" style="max-width: 100%; border-radius: 10px;">
                        </div>
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
                                <div class="col-4"><strong>Descripción</strong></div>
                                <div class="col-8"><?php echo $producto['descripcion']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Precio</strong></div>
                                <div class="col-8">S/ <?php echo $producto['precio_venta']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-4"><strong>Stock Total</strong></div>
                                <div class="col-8"><?php echo $producto['stock_total']; ?></div>
                            </div>
                            <div class="row">
                                <div class="col-4"><strong>Fecha Creación</strong></div>
                                <div class="col-8"><?php echo $producto['fecha_creacion']; ?></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6>Variantes</h6>
                    <table class="table table-sm table-striped">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php include("../../templates/footer_admin.php"); ?>
