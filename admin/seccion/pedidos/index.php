<?php 
    include("../inicio/conexion.php");

    //============== CONSULTA PEDIDOS ==============//
    $sentencia = $conexion->prepare("
    SELECT p.id_pedido, p.id_usuario, p.tipo_entrega, p.direccion, p.id_local, p.estado, p.fecha_creacion, u.nombres, u.apellidos
    FROM pedidos p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    ");
    $sentencia->execute();
    $listaPedidos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaPedidos as &$pedido) {
        foreach ($pedido as $key => $value) {
            $pedido[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($pedido); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");

    // Asignar colores según el estado
    function getEstadoColor($estado) {
        switch (strtolower($estado)) {
            case 'pendiente':
                return 'bg-warning text-dark'; // Amarillo
            case 'en proceso':
                return 'bg-info text-white'; // Azul
            case 'completado':
                return 'bg-success text-white'; // Verde
            case 'cancelado':
                return 'bg-danger text-white'; // Rojo
            default:
                return 'bg-secondary text-white'; // Gris para otros casos
        }
    }
?>

<!-- LISTA DE PEDIDOS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-shopping-basket-2-fill"></i>
            Administrar <span class="text_red">Pedidos</span>
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

            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped no-datatable">
                <thead>
                    <tr>
                        <th class="w-10 text-center">ID Pedido</th>
                        <th class="w-20 text-center">Cliente</th>
                        <th class="w-10 text-center">Tipo Entrega</th>
                        <th class="w-10 text-center">Dirección/Local</th>
                        <th class="w-20 text-center">Estado</th>
                        <th class="w-20 text-center">Fecha Creación</th>
                        <th class="w-20 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaPedidos as $pedido) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $pedido['id_pedido']; ?></td>
                            <td><?php echo $pedido['nombres'] . ' ' . $pedido['apellidos']; ?></td>
                            <td class="text-center"><?php echo ucfirst($pedido['tipo_entrega']); ?></td>
                            <td class="text-center">
                                <?php 
                                    if ($pedido['tipo_entrega'] == 'delivery') {
                                        echo $pedido['direccion'];
                                    } else {
                                        // Obtener el nombre del local
                                        $sentencia_local = $conexion->prepare("SELECT nombre_local FROM locales WHERE id_local = :id_local");
                                        $sentencia_local->bindParam(":id_local", $pedido['id_local']);
                                        $sentencia_local->execute();
                                        $local = $sentencia_local->fetch(PDO::FETCH_ASSOC);
                                        echo $local['nombre_local'];
                                    }
                                ?>
                            </td>
                            <td class="text-center <?php echo getEstadoColor($pedido['estado']); ?>">
                                <?php echo ucfirst($pedido['estado']); ?>
                            </td>
                            <td class="text-center"><?php echo date("d/m/Y H:i", strtotime($pedido['fecha_creacion'])); ?></td>
                            <td class="align-middle text-center">
                                <a class="btn btn-info btn-sm" href="ver_detalles.php?idPedido=<?php echo $pedido['id_pedido']; ?>" role="button">
                                    <i class="ri-eye-fill"></i> Ver Detalles
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $pedido['id_pedido']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus pedidos de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
