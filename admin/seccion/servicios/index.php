<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la imagen
        $sentencia = $conexion->prepare("SELECT * FROM servicio WHERE id_servicio = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $registro_imagen = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar la imagen del servidor si existe
        if (isset($registro_imagen['imagen'])) {
            $ruta_imagen = "../../../images/servicios/" . $registro_imagen['imagen'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM servicio WHERE id_servicio = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA TABLA SERVICIOS ==============//
    $sentencia = $conexion->prepare("SELECT * FROM servicio");
    $sentencia->execute();
    $listaServicios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaServicios as &$servicio) {
        foreach ($servicio as $key => $value) {
            $servicio[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($servicio); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Administrar <span class="text_red">Servicios</span>
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
    <style>
        /* Define anchos fijos para las columnas */
        .table th, .table td {
            max-width: 450px;/* Agrega '...' al final si el texto es muy largo */
        }

        /* Ajusta el ancho de las columnas específicas según sea necesario */
        .table th:nth-child(1), .table td:nth-child(1) { width: 10px; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 10px; }
        .table th:nth-child(4), .table td:nth-child(3) { width: 450px; }
        .table th:nth-child(5), .table td:nth-child(4) { width: 200px; }
        .table th:nth-child(6), .table td:nth-child(5) { width: 100px; }
        .table th:nth-child(7), .table td:nth-child(6) { width: 50px; }
    </style>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="table-responsive">

            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped no-datatable">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">NOMBRE</th>
                        <th class="text-center">DESCRIPCIÓN</th>
                        <th class="text-center">DETALLES</th>
                        <th class="text-center">FRASE</th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaServicios as $servicio) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $servicio['id_servicio']; ?></td>
                            <td class="text-center"><?php echo $servicio['nombre_servicio']; ?></td>
                            <td><?php echo $servicio['descripcion']; ?></td>
                            <td>
                                <li>
                                    <?php echo $servicio['detalle_1']; ?> </br>
                                </li> 
                                <li>   
                                    <?php echo $servicio['detalle_2']; ?> </br>
                                </li>
                                <li>
                                    <?php echo $servicio['detalle_3']; ?> </br>
                                </li> 
                                
                            </td>
                            <td><?php echo $servicio['frase']; ?></td>
                            <td class="align-middle text-center">
                                <a class="btn btn-warning btn-sm mb-2 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $servicio['id_servicio']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $servicio['id_servicio']; ?>)">
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
        <small>Gestiona tus servicios de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE SERVICIOS PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_servicio d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-group-fill"></i>
            Administrar <span class="text_red">Servicios</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaServicios as $servicio) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $servicio['id_servicio']; ?><br>
                                <strong><i class="ri-user-line"></i> Nombre:</strong> <?php echo $servicio['nombre_servicio']; ?><br>
                                <strong><i class="ri-align-left"></i> Descripción:</strong> <?php echo $servicio['descripcion']; ?><br>
                                <strong><i class="ri-calendar-line"></i> Actualizado:</strong> <?php echo $servicio['fecha_actualizacion']; ?><br> <!-- Nueva fila -->
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $servicio['id_servicio']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $servicio['id_servicio']; ?>)" style="min-width: 40px; min-height: 40px;">
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
        <small>Gestiona tus servicios de forma eficiente</small>
    </div>
</div>

<!-- BOTONES FLOTANTES EN LA PANTALLA -->
<div class="main_botones_flotantes">
        <div class="botones_fixed">
            <li>
                <a class="boton_agregar <?php echo $permiso['crear']; ?>" href="crear.php">
                    <i class="ri-add-line"></i>
                </a>
            </li>
        </div>
    </div>

<?php include("../../templates/footer_admin.php"); ?>


