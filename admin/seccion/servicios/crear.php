<?php 
    include("../inicio/conexion.php");

    if ($_POST) {
        // Obtener los datos del formulario
        $nombre_servicio = isset($_POST["nombre_servicio"]) ? $_POST["nombre_servicio"] : "";
        $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
        $detalle_1 = isset($_POST["detalle_1"]) ? $_POST["detalle_1"] : "";
        $detalle_2 = isset($_POST["detalle_2"]) ? $_POST["detalle_2"] : "";
        $detalle_3 = isset($_POST["detalle_3"]) ? $_POST["detalle_3"] : "";
        $frase = isset($_POST["frase"]) ? $_POST["frase"] : "";
        $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
        $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

        // Validaciones
        if (!preg_match("/^[a-zA-Z0-9\s]+$/", $nombre_servicio)) {
            $mensajeError = "El nombre del servicio solo debe contener letras, números y espacios.";
        } else if (strlen($nombre_servicio) < 5 || strlen($nombre_servicio) > 50) {
            $mensajeError = "El nombre del servicio debe tener entre 5 y 50 caracteres.";
        } else if (strlen($descripcion) < 10 || strlen($descripcion) > 255) {
            $mensajeError = "La descripción debe tener entre 10 y 255 caracteres.";
        } else if ($pesoImagen > 3145728) { // 3MB en bytes
            $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
        } else if ($imagen != "" && !in_array(strtolower(pathinfo($imagen, PATHINFO_EXTENSION)), ["jpg", "jpeg", "png", "gif"])) {
            $mensajeError = "Solo se permiten imágenes en formato JPG, JPEG, PNG o GIF.";
        } else {
            // Si no hay errores en las validaciones de los campos
            if ($imagen != "") {
                // Generar un nombre único para la imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];
                
                if ($tmpImagen != "") {
                    move_uploaded_file($tmpImagen, "../../../images/servicios/" . $nombreArchivo);
                }
            }

            // Insertar datos solo si no hay errores
            if (!isset($mensajeError) && $nombre_servicio != "" && $descripcion != "" && $imagen != "") {
                // =========== CONSULTA PARA INSERTAR DATOS =========== //
                $sql = "INSERT INTO servicio (nombre_servicio, descripcion, detalle_1, detalle_2, detalle_3, frase, imagen, fecha_creacion, fecha_actualizacion) 
                        VALUES (:nombre_servicio, :descripcion, :detalle_1, :detalle_2, :detalle_3, :frase, :imagen, NOW(), NOW())";
                $sentencia = $conexion->prepare($sql);

                $sentencia->bindParam(":nombre_servicio", $nombre_servicio);
                $sentencia->bindParam(":descripcion", $descripcion);
                $sentencia->bindParam(":detalle_1", $detalle_1);
                $sentencia->bindParam(":detalle_2", $detalle_2);
                $sentencia->bindParam(":detalle_3", $detalle_3);
                $sentencia->bindParam(":frase", $frase);
                $sentencia->bindParam(":imagen", $nombreArchivo);

                $sentencia->execute();

                header("Location: index.php");
            } else if (!isset($mensajeError)) {
                $mensajeError = "Todos los campos son obligatorios, incluyendo la imagen.";
            }
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Agregar <span class="text_red">Servicio</span>
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

        <form id="form_servicio" action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="nombre_servicio" class="form-label">
                        <i class="ri-price-tag-3-fill"></i> Nombre del Servicio
                    </label>
                    <input type="text" class="form-control" name="nombre_servicio" id="nombre_servicio" placeholder="Escriba el nombre del servicio" required minlength="5" maxlength="50" pattern="[a-zA-Z0-9\s]+" />
                </div>

                <div class="col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-align-left"></i> Descripción
                    </label>
                    <textarea class="form-control" name="descripcion" id="descripcion" rows="4" placeholder="Descripción del servicio" required minlength="10" maxlength="255"></textarea>
                </div>

                <div class="col-md-4 col-12 mb-3">
                    <label for="detalle_1" class="form-label">
                        <i class="ri-list-unordered"></i> Detalle 1
                    </label>
                    <input type="text" class="form-control" name="detalle_1" id="detalle_1" placeholder="Primer detalle" required />
                </div>

                <div class="col-md-4 col-12 mb-3">
                    <label for="detalle_2" class="form-label">
                        <i class="ri-list-unordered"></i> Detalle 2
                    </label>
                    <input type="text" class="form-control" name="detalle_2" id="detalle_2" placeholder="Segundo detalle" required />
                </div>

                <div class="col-md-4 col-12 mb-3">
                    <label for="detalle_3" class="form-label">
                        <i class="ri-list-unordered"></i> Detalle 3
                    </label>
                    <input type="text" class="form-control" name="detalle_3" id="detalle_3" placeholder="Tercer detalle" required />
                </div>

                <div class="col-12 mb-3">
                    <label for="frase" class="form-label">
                        <i class="ri-quotation-mark"></i> Frase
                    </label>
                    <input type="text" class="form-control" name="frase" id="frase" placeholder="Frase del servicio" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-line"></i> Imagen
                    </label>
                    <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*" onchange="previewImage(event)" required />
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
                    <i class="ri-add-circle-line"></i> Crear Servicio
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus servicios de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
