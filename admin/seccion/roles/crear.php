<?php 
    include("../inicio/conexion.php");

    if ($_POST) {
        $rol_nombre = isset($_POST["rol_nombre"]) ? $_POST["rol_nombre"] : "";
        $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
        $estado = isset($_POST["estado"]) ? $_POST["estado"] : 1;

        // Insertar datos solo si los campos están completos
        if ($rol_nombre != "" && $descripcion != "") {
            // =========== CONSULTA PARA INSERTAR DATOS =========== //
            $sql = "INSERT INTO `roles` (`id_rol`, `rol_nombre`, `descripcion`, `estado`) 
                    VALUES (NULL, :rol_nombre, :descripcion, :estado)";
            $sentencia = $conexion->prepare($sql);

            $sentencia->bindParam(":rol_nombre", $rol_nombre);
            $sentencia->bindParam(":descripcion", $descripcion);
            $sentencia->bindParam(":estado", $estado);

            $sentencia->execute();

            header("Location:index.php");
        } else {
            $mensajeError = "Todos los campos son obligatorios.";
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">

    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-settings-fill"></i>
            Agregar <span class="text_red">Rol</span>
        </span>

        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="index.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Atras
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <?php if (isset($mensajeError)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $mensajeError; ?>
            </div>
        <?php } ?>

        <form id="form_rol" action="" method="post">
            <div class="row">

                <div class="col-md-6 col-12 mb-3">
                    <label for="rol_nombre" class="form-label">
                        <i class="ri-user-settings-fill"></i> Nombre del Rol
                    </label>
                    <input type="text" class="form-control" name="rol_nombre" id="rol_nombre" placeholder="Escriba el nombre del rol" autofocus required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-file-text-line"></i> Descripción
                    </label>
                    <input type="text" class="form-control" name="descripcion" id="descripcion" placeholder="Escriba una descripción del rol" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="estado" class="form-label">
                        <i class="ri-toggle-fill"></i> Estado
                    </label>
                    <select class="form-control" name="estado" id="estado">
                        <option value="1" selected>Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3">
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-add-circle-line"></i> Crear Rol
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus roles de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
