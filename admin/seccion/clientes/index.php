<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la foto
        $sentencia = $conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $registro_foto = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar la foto del servidor si no es "default.png"
        if (isset($registro_foto['foto']) && $registro_foto['foto'] !== 'usuario_default.png') {
            $ruta_foto = "../../../images/clientes/" . $registro_foto['foto'];
            if (file_exists($ruta_foto)) {
                unlink($ruta_foto);
            }
        }

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA SOLO CLIENTES ==============//
    $sentencia = $conexion->prepare("SELECT u.*, r.rol_nombre 
        FROM usuarios u
        LEFT JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.id_rol = 5  -- Asumiendo que el rol de cliente es 3
    ");
    $sentencia->execute();
    $listaClientes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaClientes as &$cliente) {
        foreach ($cliente as $key => $value) {
            $cliente[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($cliente); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>
    <style>
        /* Define anchos fijos para las columnas */
        .table th, .table td {
            max-width: 250px; /* Ajusta el ancho máximo según sea necesario */
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis; /* Agrega '...' al final si el texto es muy largo */
        }

        /* Ajusta el ancho de las columnas específicas según sea necesario */
        .table th:nth-child(1), .table td:nth-child(1) { width: 20px; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 100px; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 100px; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 100px; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 80px; }
        .table th:nth-child(6), .table td:nth-child(6) { width: 100px; }
        .table th:nth-child(7), .table td:nth-child(7) { width: 50px; }
        .table th:nth-child(7), .table td:nth-child(8) { width: 50px; }
        .table th:nth-child(8), .table td:nth-child(9) { width: 130px; }
    </style>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-2-fill"></i>
            Administrar <span class="text_red">Clientes</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i> Página Principal
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-light table-hover align-middle table-striped table-fixed">
                <thead>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nombres</th>
                        <th class="text-center">Apellidos</th>
                        <th class="text-center">Correo</th>
                        <th class="text-center">Teléfono</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaClientes as $cliente) { ?>
                        <tr class="align-middle">
                            <td class="text-center"><?php echo $cliente['id_usuario']; ?></td>
                            <td class="text-center"><?php echo $cliente['nombres']; ?></td>
                            <td class="text-center"><?php echo $cliente['apellidos']; ?></td>
                            <td><?php echo $cliente['correo']; ?></td>
                            <td class="text-center"><?php echo $cliente['telefono'] ?? 'N/A'; ?></td>
                            <td class="text-center">
                                <?php echo $cliente['estado'] == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>
                            </td>
                            <td class="align-middle text-center">
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerCliente<?php echo $cliente['id_usuario']; ?>">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <a class="btn btn-warning btn-sm <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $cliente['id_usuario']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $cliente['id_usuario']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus clientes de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE REGISTROS PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-2-fill"></i>
            Administrar <span class="text_red">Clientes</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i> Página Principal
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <div class="list-group">
            <?php foreach($listaClientes as $cliente) { ?>
                <div class="list-group-item" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="mb-0"><?php echo $cliente['nombres'] . ' ' . $cliente['apellidos']; ?></h6>
                            <small"><?php echo $cliente['correo']; ?></small>
                        </div>
                        <img src="<?php 
                            $rutaImagen = "../../../images/clientes/" . $cliente['foto'];
                            if (!file_exists($rutaImagen) || empty($cliente['foto'])) {
                                $rutaImagen = "../../../images/default/usuario_default.png";
                            }
                            echo $rutaImagen; 
                        ?>" alt="Foto" width="40" height="40" style="border-radius: 50%;">
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <small">Tel: <?php echo $cliente['telefono'] ?? 'N/A'; ?></small><br>
                            <small">Estado: 
                                <?php echo $cliente['estado'] == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerCliente<?php echo $cliente['id_usuario']; ?>">
                                <i class="ri-eye-line"></i>
                            </button>
                            <a class="btn btn-warning btn-sm <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $cliente['id_usuario']; ?>" role="button">
                                <i class="ri-pencil-fill"></i>
                            </a>
                            <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $cliente['id_usuario']; ?>)">
                                <i class="ri-delete-bin-2-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus clientes desde tu dispositivo móvil</small>
    </div>
</div>

<!-- Modal para cada cliente -->
<?php foreach($listaClientes as $cliente) { ?>
    <div class="modal fade" id="modalVerCliente<?php echo $cliente['id_usuario']; ?>" tabindex="-1" aria-labelledby="modalVerClienteLabel<?php echo $cliente['id_usuario']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="width: 90%; margin:auto; max-width: 450px;">
            <div class="modal-content rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <div class="modal-header bg-danger text-white rounded-0">
                    <h5 class="modal-title" id="modalVerClienteLabel<?php echo $cliente['id_usuario']; ?>">Detalles del Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-5"><strong>DNI</strong></div>
                        <div class="col-7"><?php echo $cliente['dni'] ?? 'N/A'; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Nombres</strong></div>
                        <div class="col-7"><?php echo $cliente['nombres']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Apellidos</strong></div>
                        <div class="col-7"><?php echo $cliente['apellidos']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Usuario</strong></div>
                        <div class="col-7"><?php echo $cliente['usuario']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Correo</strong></div>
                        <div class="col-7"><?php echo $cliente['correo']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Teléfono</strong></div>
                        <div class="col-7"><?php echo $cliente['telefono']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Dirección</strong></div>
                        <div class="col-7"><?php echo $cliente['direccion']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Género</strong></div>
                        <div class="col-7"><?php echo $cliente['genero']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Rol</strong></div>
                        <div class="col-7"><?php echo $cliente['rol_nombre']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Estado</strong></div>
                        <div class="col-7">
                            <?php echo $cliente['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Fecha de Creación</strong></div>
                        <div class="col-7"><?php echo $cliente['fecha_creacion']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Último Acceso</strong></div>
                        <div class="col-7"><?php echo $cliente['fecha_ultimo_acceso']; ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-5"><strong>Foto</strong></div>
                        <div class="col-7">
                            <img src="<?php echo $rutaImagen; ?>" width="100" alt="Sin foto" style="border-radius: 5px;">
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
