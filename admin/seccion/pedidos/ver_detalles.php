<?php 
    include("../inicio/conexion.php");

    // Verificar si se pasa el idPedido en la URL
    if (isset($_GET['idPedido'])) {
        $idPedido = $_GET['idPedido'];

        // Consultar los detalles del pedido
        $sentencia = $conexion->prepare("
            SELECT p.id_pedido, p.id_usuario, p.tipo_entrega, p.direccion, p.id_local, p.estado, p.fecha_creacion, u.nombres, u.apellidos
            FROM pedidos p
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.id_pedido = :id_pedido
        ");
        $sentencia->bindParam(":id_pedido", $idPedido, PDO::PARAM_INT);
        $sentencia->execute();
        $pedido = $sentencia->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el pedido
        if (!$pedido) {
            echo "Pedido no encontrado";
            exit;
        }

        // Escapar los datos de la base de datos con htmlspecialchars
        foreach ($pedido as $key => $value) {
            $pedido[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        // Obtener los productos del pedido
        $productosSentencia = $conexion->prepare("
            SELECT pp.id_pedido_producto, pp.cantidad, pp.precio_unitario, pp.precio_total, pv.id_variante, pr.nombre_producto, c.nombre_color, t.nombre_talla
            FROM pedidos_productos pp
            JOIN productos_variantes pv ON pp.id_variante = pv.id_variante
            JOIN productos pr ON pv.id_producto = pr.id_producto
            JOIN colores c ON pv.id_color = c.id_color
            JOIN tallas t ON pv.id_talla = t.id_talla
            WHERE pp.id_pedido = :id_pedido
        ");
        $productosSentencia->bindParam(":id_pedido", $idPedido, PDO::PARAM_INT);
        $productosSentencia->execute();
        $productos = $productosSentencia->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener el local si el pedido es de tipo 'retirar'
        if ($pedido['tipo_entrega'] == 'retirar') {
            $localSentencia = $conexion->prepare("SELECT nombre_local FROM locales WHERE id_local = :id_local");
            $localSentencia->bindParam(":id_local", $pedido['id_local']);
            $localSentencia->execute();
            $local = $localSentencia->fetch(PDO::FETCH_ASSOC);
        }
    } else {
        echo "ID de pedido no especificado";
        exit;
    }

    include("../../templates/header_admin.php");
?>

<!-- DETALLES DEL PEDIDO -->
<div class="card shadow-sm">
    <div class="card-header" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-shopping-basket-2-fill"></i>
            Detalles del <span class="text_red">Pedido</span> 
        </span>
        <a name="atras" id="atras" class="btn btn-primary" href="index.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Volver a la lista de pedidos
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <div class="col-md-6">
                <h5>ID Pedido: <?php echo $pedido['id_pedido']; ?></h5>
                <p><strong>Cliente:</strong> <?php echo $pedido['nombres'] . ' ' . $pedido['apellidos']; ?></p>
                <p><strong>Tipo de Entrega:</strong> <?php echo ucfirst($pedido['tipo_entrega']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Estado:</strong> <?php echo ucfirst($pedido['estado']); ?></p>
                <p><strong>Fecha de Creación:</strong> <?php echo date("d/m/Y H:i", strtotime($pedido['fecha_creacion'])); ?></p>
            </div>
        </div>

        <!-- Mostrar Dirección o Local según el tipo de entrega -->
        <div class="row">
            <?php if ($pedido['tipo_entrega'] == 'delivery'): ?>
                <div class="col-md-6">
                    <p><strong>Dirección de Entrega:</strong> <?php echo $pedido['direccion']; ?></p>
                </div>
            <?php else: ?>
                <div class="col-md-6">
                    <p><strong>Local de Recogida:</strong> <?php echo $local['nombre_local']; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Mostrar los productos del pedido -->
        <h6>Productos del Pedido:</h6>
        <table class="table no-datatable">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Precio Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo $producto['nombre_producto']; ?></td>
                        <td><?php echo $producto['nombre_talla']; ?></td>
                        <td><?php echo $producto['nombre_color']; ?></td>
                        <td><?php echo $producto['cantidad']; ?></td>
                        <td><?php echo number_format($producto['precio_unitario'], 2); ?></td>
                        <td><?php echo number_format($producto['precio_total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
