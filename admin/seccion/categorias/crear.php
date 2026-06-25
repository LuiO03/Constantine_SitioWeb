<?php 
    include("../inicio/conexion.php");

    if ($_POST) {
        
        $nombre_categoria = isset($_POST["nombre_categoria"]) ? $_POST["nombre_categoria"] : "";
        $ubicacion = isset($_POST["ubicacion"]) ? $_POST["ubicacion"] : "";
        $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
        $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

        // Si se ha subido una imagen
        if ($imagen != "") {
            // Validar tamaño de la imagen (máximo 3MB)
            if ($pesoImagen > 3145728) { // 3MB en bytes = 3 * 1024 * 1024 = 3145728
                $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
            } else {
                // Generar un nombre único para la imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];

                if ($tmpImagen != "") {
                    move_uploaded_file($tmpImagen, "../../../images/categorias/" . $nombreArchivo);
                }
            }
        }

        // Insertar datos solo si los campos están completos y no hay error en la imagen
        if (!isset($mensajeError) && $nombre_categoria != "" && $ubicacion != "" && $imagen != "") {
            // =========== CONSULTA PARA INSERTAR DATOS =========== //
            $sql = "INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`, `ubicacion`, `imagen`) 
                    VALUES (NULL, :nombre_categoria, :ubicacion, :imagen)";
            $sentencia = $conexion->prepare($sql);

            $sentencia->bindParam(":nombre_categoria", $nombre_categoria);
            $sentencia->bindParam(":ubicacion", $ubicacion);
            $sentencia->bindParam(":imagen", $nombreArchivo);

            $sentencia->execute();

            header("Location:index.php");
        } else if (!isset($mensajeError)) {
            $mensajeError = "Todos los campos son obligatorios, incluyendo la imagen.";
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">

    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Agregar <span class="text_red">SubCategoría</span>
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

        <form id="form_categoria" action="" method="post" enctype="multipart/form-data">
            <div class="row">

                <div class="col-md-6 col-12 mb-3">
                    <label for="nombre_categoria" class="form-label">
                        <i class="ri-price-tag-3-fill"></i> Nombre de Categoría
                    </label>
                    <input type="text" class="form-control" name="nombre_categoria" id="nombre_categoria" placeholder="Escriba el nombre de la categoría" autofocus required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="ubicacion" class="form-label">
                        <i class="ri-map-pin-line"></i> Ubicación
                    </label>
                    <input type="text" class="form-control" name="ubicacion" id="ubicacion" placeholder="Escriba la ubicación" required />
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
                    <i class="ri-add-circle-line"></i> Crear Categoría
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus categorías de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
