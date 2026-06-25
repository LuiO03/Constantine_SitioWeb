<?php 
    include("../inicio/conexion.php");

    //============== CONSULTA TABLA REDES_SOCIALES ==============//
    $sentencia = $conexion->prepare("SELECT * FROM redes_sociales");
    $sentencia->execute();
    $listaRedesSociales = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    foreach ($listaRedesSociales as &$red) {
        foreach ($red as $key => $value) {
            $red[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($red);

    //============== CONSULTA PARA OBTENER DATOS DE CONTACTOS ==============//
    $sql = "SELECT * FROM contactos_negocio LIMIT 1";
    $sentencia = $conexion->prepare($sql);
    $sentencia->execute();
    $contacto = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura de los datos editados del formulario
        $hora_atencion = $_POST["hora_atencion"] ?? "";
        $direccion = $_POST["direccion"] ?? "";
        $correo = $_POST["correo"] ?? "";
        $telefono = $_POST["telefono"] ?? "";
        $celular = $_POST["celular"] ?? "";
    
        if ($hora_atencion && $direccion && $correo && $telefono && $celular) { 
            // Actualizar los datos del contacto
            $sql = "UPDATE contactos_negocio SET hora_atencion = :hora_atencion, direccion = :direccion, correo = :correo, telefono = :telefono, celular = :celular";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":hora_atencion", $hora_atencion);
            $sentencia->bindParam(":direccion", $direccion);
            $sentencia->bindParam(":correo", $correo);
            $sentencia->bindParam(":telefono", $telefono);
            $sentencia->bindParam(":celular", $celular);
            $sentencia->execute();
    
            header("Location:index.php");
        } else {
            $mensajeError = "Todos los campos son obligatorios.";
        }
    }

    include("../../templates/header_admin.php");
?>
<div class="container">
    <div class="row">
        <!-- Información del Negocio -->
        <div class="col-md-5">
            <div class="card shadow-sm rounded-0">
                <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                    <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
                        <i class="ri-contacts-book-line"></i>
                        Información del<span class="text_red"> Negocio</span>
                    </span>
                </div>
                <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                    <!-- Mensaje de error si faltan campos obligatorios -->
                    <?php if (isset($mensajeError)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $mensajeError; ?>
                        </div>
                    <?php } ?>

                    <!-- Formulario de contacto -->
                    <form id="form_contacto" action="" method="post">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="hora_atencion" class="form-label"><i class="ri-time-line"></i> Horario de Atención</label>
                                <input type="text" class="form-control" name="hora_atencion" id="hora_atencion" placeholder="Ej. Lunes a Viernes, 9am - 6pm" value="<?php echo $contacto['hora_atencion']; ?>" required />
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="direccion" class="form-label"><i class="ri-map-pin-line"></i> Dirección</label>
                                <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Escriba la dirección" value="<?php echo $contacto['direccion']; ?>" required />
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="correo" class="form-label"><i class="ri-mail-line"></i> Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo" id="correo" placeholder="correo@ejemplo.com" value="<?php echo $contacto['correo']; ?>" required />
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="telefono" class="form-label"><i class="ri-phone-line"></i> Teléfono</label>
                                <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="Teléfono" value="<?php echo $contacto['telefono']; ?>" required />
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="celular" class="form-label"><i class="ri-smartphone-line"></i> Número Móvil</label>
                                <input type="tel" class="form-control" name="celular" id="celular" placeholder="Número Móvil" value="<?php echo $contacto['celular']; ?>" required />
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-start gap-3 mb-4">
                            <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                                <i class="ri-save-3-fill"></i> Guardar Cambios
                            </button>
                            <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                                <i class="ri-close-line"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Redes Sociales -->
        <style>
            /* Define anchos fijos para las columnas */
            .table th, .table td {
                max-width: 150px; /* Ajusta el ancho máximo según sea necesario */
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis; /* Agrega '...' al final si el texto es muy largo */
            }

            /* Ajusta el ancho de las columnas específicas según sea necesario */
            .table th:nth-child(1), .table td:nth-child(1) { width: 20px; }
            .table th:nth-child(2), .table td:nth-child(2) { width: 100px; }
            .table th:nth-child(3), .table td:nth-child(3) { width: 50px; }
            .table th:nth-child(4), .table td:nth-child(4) { width: 100px; }
            .table th:nth-child(5), .table td:nth-child(5) { width: 80px; }
        </style>
        <div class="col-md-7">
            <div class="card shadow-sm rounded-0">
                <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                    <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
                        <i class="ri-global-line"></i>
                        Redes<span class="text_red"> Sociales</span>
                    </span>
                </div>
                <div class="card-body table-responsive" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                    <table class="table table-sm table-dark table-bordered table-hover align-middle table-fixed">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">TÍTULO</th>
                                <th class="text-center">ENLACE</th>
                                <th class="text-center">ESTADO</th>
                                <th class="text-center">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($listaRedesSociales as $red) { ?>
                                <tr>
                                    <td class="text-center"><?php echo $red['id_red']; ?></td>
                                    <td><?php echo $red['titulo']; ?></td>
                                    <td><a href="<?php echo $red['enlace']; ?>" target="_blank"><?php echo $red['enlace']; ?></a></td>
                                    <td class="text-center">
                                        <?php if ($red['estado'] == 1) { ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $red['id_red']; ?>" role="button">
                                            <i class="ri-pencil-fill"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LISTA DE REDES SOCIALES PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_redes d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-global-line"></i>
            Administrar <span class="text_red">Redes Sociales</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus redes sociales de forma eficiente</small>
    </div>
</div>

<script src="../../js/script.js"></script>
<script src="../../js/formulario.js"></script>
<!-- Bootstrap JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
    crossorigin="anonymous">
</script>

