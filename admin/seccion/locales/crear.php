<?php 
    include("../inicio/conexion.php");

    if ($_POST) {
        // Obtener los datos del formulario
        $nombre_local = trim($_POST["nombre_local"] ?? "");
        $direccion = trim($_POST["direccion"] ?? "");
        $horario = trim($_POST["horario"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $enlace = trim($_POST["enlace"] ?? "");
        $imagen = $_FILES["imagen"]["name"] ?? "";
        $pesoImagen = $_FILES["imagen"]["size"] ?? 0;

        // Validar campos
        if (strlen($nombre_local) < 3 || strlen($nombre_local) > 50 || !preg_match("/^[a-zA-Z0-9\s]+$/", $nombre_local)) {
            $mensajeError = "El nombre del local debe tener entre 3 y 50 caracteres y solo puede contener letras, números y espacios.";
        } elseif (strlen($direccion) < 10 || strlen($direccion) > 150) {
            $mensajeError = "La dirección debe tener entre 10 y 150 caracteres.";
        } elseif (strlen($horario) < 5 || strlen($horario) > 100) {
            $mensajeError = "El horario debe tener entre 5 y 100 caracteres.";
        } elseif (!empty($telefono) && !preg_match("/^[0-9]{9}$/", $telefono)) {
            $mensajeError = "El teléfono debe ser un número válido de 9 dígitos.";
        } elseif (!filter_var($enlace, FILTER_VALIDATE_URL)) {
            $mensajeError = "El enlace debe ser una URL válida.";
        } elseif ($pesoImagen > 3145728) { // 3MB
            $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
        }

        // Si no hay errores, procesar imagen y guardar en la base de datos
        if (!isset($mensajeError) && $imagen != "") {
            $fecha = new DateTime();
            $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
            $tmpImagen = $_FILES["imagen"]["tmp_name"];

            if ($tmpImagen != "") {
                move_uploaded_file($tmpImagen, "../../../images/locales/" . $nombreArchivo);
            }
        }

        // Insertar datos solo si no hay errores
        if (!isset($mensajeError)) {
            $sql = "INSERT INTO locales (nombre_local, direccion, horario, telefono, enlace, imagen, fecha_creacion, fecha_actualizacion) 
                    VALUES (:nombre_local, :direccion, :horario, :telefono, :enlace, :imagen, NOW(), NOW())";
            $sentencia = $conexion->prepare($sql);

            $sentencia->bindParam(":nombre_local", $nombre_local);
            $sentencia->bindParam(":direccion", $direccion);
            $sentencia->bindParam(":horario", $horario);
            $sentencia->bindParam(":telefono", $telefono);
            $sentencia->bindParam(":enlace", $enlace);
            $sentencia->bindParam(":imagen", $nombreArchivo);

            $sentencia->execute();

            header("Location: index.php");
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-store-2-fill"></i>
            Agregar <span class="text_red">Local</span>
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

        <form id="form_local" action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="nombre_local" class="form-label">
                        <i class="ri-store-2-fill"></i> Nombre del Local
                    </label>
                    <input type="text" class="form-control" name="nombre_local" id="nombre_local" placeholder="Escriba el nombre del local" required 
                           pattern="^[a-zA-Z0-9\s]{3,50}$" maxlength="50" minlength="3" title="Solo letras, números y espacios. Mínimo 3 y máximo 50 caracteres." />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="direccion" class="form-label">
                        <i class="ri-map-pin-line"></i> Dirección
                    </label>
                    <textarea class="form-control" name="direccion" id="direccion" rows="1" placeholder="Dirección del local" required maxlength="150" minlength="10" title="Debe contener entre 10 y 150 caracteres."></textarea>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="horario" class="form-label">
                        <i class="ri-time-line"></i> Horario
                    </label>
                    <textarea class="form-control" name="horario" id="horario" rows="1" placeholder="Horario del local" required maxlength="100" minlength="5" title="Debe contener entre 5 y 100 caracteres."></textarea>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="telefono" class="form-label">
                        <i class="ri-phone-line"></i> Teléfono
                    </label>
                    <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono del local" 
                           pattern="^[0-9]{9}$" maxlength="9" title="Debe ser un número de 9 dígitos." />
                </div>

                <div class="col-12 mb-3">
                    <label for="enlace" class="form-label">
                        <i class="ri-link"></i> Enlace
                    </label>
                    <input type="url" class="form-control" name="enlace" id="enlace" placeholder="Enlace del local en Google Maps" required maxlength="255" 
                           pattern="https?://.+" title="Debe ser una URL válida que comience con http:// o https://" />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-line"></i> Imagen
                    </label>
                    <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*" onchange="previewImage(event)" required title="Solo se permiten imágenes de máximo 3MB." />
                </div>

                <div class="col-md-6 col-12 mb-3 d-flex flex-column">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-2-line"></i> Previsualización de la Imagen:
                    </label>
                    <img id="preview" src="" alt="Vista previa de la imagen" class="img-fluid rounded d-none border w-25" style="cursor: pointer; transition: width 0.5s ease;" onclick="toggleImageSize()"/>
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3">
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-add-circle-line"></i> Crear Local
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus locales de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
