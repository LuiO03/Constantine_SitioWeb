<?php 
    include("../inicio/conexion.php");

    //============== OBTENER ID DEL PÚBLICO A EDITAR ==============//
    $txtID = isset($_GET['txtID']) ? htmlspecialchars(trim($_GET['txtID'])) : "";

    //============== CONSULTA PARA OBTENER DATOS DEL PÚBLICO ==============//
    $sql = "SELECT * FROM publico WHERE id_publico = :id";
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $publico = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura y sanitización de los datos del formulario
        $nombre_publico = isset($_POST["nombre_publico"]) ? htmlspecialchars(trim($_POST["nombre_publico"])) : "";
        $descripcion = isset($_POST["descripcion"]) ? htmlspecialchars(trim($_POST["descripcion"])) : "";

        // Validaciones del lado del servidor
        if (empty($nombre_publico) || strlen($nombre_publico) < 3 || strlen($nombre_publico) > 50) {
            $mensajeError = "El nombre del público debe tener entre 3 y 50 caracteres.";
        } elseif (empty($descripcion) || strlen($descripcion) < 10 || strlen($descripcion) > 200) {
            $mensajeError = "La descripción debe tener entre 10 y 200 caracteres.";
        } 

        if (!isset($mensajeError)) {
            // Actualizar los datos del público
            $sql = "UPDATE publico SET nombre_publico = :nombre_publico, descripcion = :descripcion WHERE id_publico = :id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":nombre_publico", $nombre_publico);
            $sentencia->bindParam(":descripcion", $descripcion);
            $sentencia->bindParam(":id", $txtID);
            $sentencia->execute();

            header("Location:index.php");
            exit();
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-group-2-fill"></i>
            Editar Público: <span class="text_red">&nbsp; N° <?php echo htmlspecialchars($publico['id_publico']); ?> ~ <?php echo htmlspecialchars($publico['nombre_publico']); ?></span>
        </span>

        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="index.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Atrás
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <?php if (isset($mensajeError)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $mensajeError; ?>
            </div>
        <?php } ?>

        <form id="form_publico" action="" method="post" novalidate>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="nombre_publico" class="form-label">
                        <i class="ri-user-line"></i> Nombre del Público
                    </label>
                    <input type="text" class="form-control" name="nombre_publico" id="nombre_publico" placeholder="Escriba el nombre del público" value="<?php echo htmlspecialchars($publico['nombre_publico']); ?>" minlength="3" maxlength="50" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-align-left"></i> Descripción
                    </label>
                    <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Escriba una descripción" minlength="10" maxlength="200" required><?php echo htmlspecialchars($publico['descripcion']); ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3">
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-save-3-fill"></i> Guardar Cambios
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tu público de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
