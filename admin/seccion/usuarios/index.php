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
        if (isset($registro_foto['foto']) && $registro_foto['foto'] !== 'default.png') {
            $ruta_foto = "../../../images/usuarios/" . $registro_foto['foto'];
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

    //============== CONSULTA TABLA USUARIOS CON JOIN A ROL ==============//
    $sentencia = $conexion->prepare("SELECT u.*, r.rol_nombre 
    FROM usuarios u
    LEFT JOIN roles r ON u.id_rol = r.id_rol
    WHERE u.id_rol != 5
    ");
    $sentencia->execute();
    $listaUsuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaUsuarios as &$usuario) {
        foreach ($usuario as $key => $value) {
            $usuario[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($usuario); // Evitar referencias no deseadas

    $sentencia=$conexion->prepare("SELECT * FROM banners WHERE pagina = 'inicio' AND estado = 1 ORDER BY id_banner DESC;");
    $sentencia->execute();
    $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);
   
    
    include("../../templates/header_admin.php");
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario_logueado = $_SESSION['id_usuario'];
    }
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
            Administrar <span class="text_red">Usuarios</span>
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
                        <th class="text-center">Rol</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Foto</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaUsuarios as $usuario) { ?>
                        <tr class="align-middle">
                            <td class="text-center"><?php echo $usuario['id_usuario']; ?></td>
                            <td><?php echo $usuario['nombres']; ?></td>
                            <td><?php echo $usuario['apellidos']; ?></td>
                            <td><?php echo $usuario['correo']; ?></td>
                            <td><?php echo $usuario['telefono'] ?? 'N/A'; ?></td>
                            <td><?php echo $usuario['rol_nombre'] ?? 'N/A'; ?></td>
                            <td class="text-center">
                                <?php echo $usuario['estado'] == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php
                                    $rutaImagen = "../../../images/usuarios/" . $usuario['foto'];
                                
                                    if (!file_exists($rutaImagen) || empty($usuario['foto'])) {
                                        $rutaImagen = "../../../images/default/usuario_default.png";
                                    }
                                ?>
                                <img src="<?php echo $rutaImagen; ?>" width="50" alt="no hay foto" style="border-radius: 5px;">
                            </td>
                            <td class="align-middle text-center">
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerUsuario<?php echo $usuario['id_usuario']; ?>">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <a class="btn btn-warning btn-sm <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $usuario['id_usuario']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <!-- Verificar si el usuario logueado no es el que está intentando eliminar -->
                                <?php if ($usuario['id_usuario'] != $id_usuario_logueado): ?>
                                    <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $usuario['id_usuario']; ?>)">
                                        <i class="ri-delete-bin-2-fill"></i>
                                    </button>
                                <?php else: ?>
                                    <!-- Si es el mismo usuario logueado, no se muestra el botón -->
                                    <button class="btn btn-danger btn-sm" disabled>
                                        <i class="ri-delete-bin-2-fill"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus usuarios de forma eficiente</small>
    </div>
</div>



<!-- LISTA DE USUARIOS PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-2-fill"></i>
            Administrar <span class="text_red">Usuarios</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaUsuarios as $usuario) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $usuario['id_usuario']; ?><br>
                                <strong><i class="ri-user-line"></i> Usuario:</strong> <?php echo $usuario['usuario']; ?><br>
                                <strong><i class="ri-mail-line"></i> Correo:</strong> <?php echo $usuario['correo']; ?><br>
                                <strong><i class="ri-phone-line"></i> Teléfono:</strong> <?php echo $usuario['telefono'] ?? 'N/A'; ?><br>
                                <strong><i class="ri-user-star-line"></i> Rol:</strong> <?php echo $usuario['rol_nombre'] ?? 'N/A'; ?><br>
                                <strong><i class="ri-time-line"></i> Último Acceso:</strong> <?php echo $usuario['fecha_ultimo_acceso'] ?? 'N/A'; ?><br>
                                <strong><i class="ri-checkbox-circle-line"></i> Estado:</strong>
                                <?php if ($usuario['estado'] == 1) { ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php } ?>
                                <br>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <a class="btn btn-warning btn-sm mx-1" href="editar.php?txtID=<?php echo $usuario['id_usuario']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button class="btn btn-info btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#modalVerUsuario<?php echo $usuario['id_usuario']; ?>">
                                    <i class="ri-eye-fill"></i> Ver
                                </button>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $usuario['id_usuario']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus usuarios de forma eficiente</small>
    </div>
</div>

<!-- Aquí es donde movemos la modal fuera de ventana_escritorio -->
<?php foreach($listaUsuarios as $usuario) { ?>
    <!-- Modal para mostrar los detalles del usuario -->
<div class="modal fade" id="modalVerUsuario<?php echo $usuario['id_usuario']; ?>" tabindex="-1" aria-labelledby="modalVerUsuarioLabel<?php echo $usuario['id_usuario']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width: 90%; margin:auto; max-width: 400px;"> <!-- Ajustes responsivos y centrado -->
        <div class="modal-content rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
            <div class="modal-header bg-danger text-white rounded-0">
                <h5 class="modal-title" id="modalVerUsuarioLabel<?php echo $usuario['id_usuario']; ?>">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <!-- Simulación de una tabla con divs -->
                <div class="row mb-3">
                    <div class="col-5"><strong>DNI</strong></div>
                    <div class="col-7"><?php echo $usuario['dni']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Nombres</strong></div>
                    <div class="col-7"><?php echo $usuario['nombres']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Apellidos</strong></div>
                    <div class="col-7"><?php echo $usuario['apellidos']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Correo</strong></div>
                    <div class="col-7"><?php echo $usuario['correo']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Dirección</strong></div>
                    <div class="col-7"><?php echo $usuario['direccion']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Teléfono</strong></div>
                    <div class="col-7"><?php echo $usuario['telefono'] ?? 'N/A'; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Rol</strong></div>
                    <div class="col-7"><?php echo $usuario['rol_nombre'] ?? 'N/A'; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Estado</strong></div>
                    <div class="col-7"><?php echo $usuario['estado'] == 1 ? 'Activo' : 'Inactivo'; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Último Acceso</strong></div>
                    <div class="col-7"><?php echo $usuario['fecha_ultimo_acceso'] ?? 'N/A'; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Fecha de Creación</strong></div>
                    <div class="col-7"><?php echo $usuario['fecha_creacion']; ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-5"><strong>Foto</strong></div>
                    <div class="col-7">
                        <?php
                            $rutaImagen = "../../../images/usuarios/" . $usuario['foto'];
                        
                            if (!file_exists($rutaImagen) || empty($usuario['foto'])) {
                                $rutaImagen = "../../../images/default/usuario_default.png";
                            }
                        ?>
                        <img src="<?php echo $rutaImagen; ?>" width="50" alt="no hay foto" style="border-radius: 5px;">
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

