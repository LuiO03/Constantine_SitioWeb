<?php 
    include("../inicio/conexion.php");

    //============== OBTENER ID DEL BANNER A EDITAR ==============//
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

    //============== CONSULTA PARA OBTENER DATOS DEL BANNER ==============//
    $sql = "SELECT * FROM banners WHERE id_banner = :id";
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $banner = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura y sanitización de los datos del formulario
        $titulo = isset($_POST["titulo"]) ? htmlspecialchars(trim($_POST["titulo"])) : "";
        $enfasis = isset($_POST["enfasis"]) ? htmlspecialchars(trim($_POST["enfasis"])) : "";
        $descripcion = isset($_POST["descripcion"]) ? htmlspecialchars(trim($_POST["descripcion"])) : "";
        $orden = isset($_POST["orden"]) ? filter_var($_POST["orden"], FILTER_VALIDATE_INT) : 0;
        $estado = isset($_POST["estado"]) ? filter_var($_POST["estado"], FILTER_VALIDATE_INT) : 1;
        $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
        $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

        // Validaciones del lado del servidor
        if (empty($titulo) || strlen($titulo) > 100) {
            $mensajeError = "El título es obligatorio y no debe exceder los 100 caracteres.";
        } elseif (empty($enfasis) || strlen($enfasis) > 50) {
            $mensajeError = "El énfasis es obligatorio y no debe exceder los 50 caracteres.";
        } elseif (strlen($descripcion) > 255) {
            $mensajeError = "La descripción no debe exceder los 255 caracteres.";
        } elseif ($orden === false || $orden < 0) {
            $mensajeError = "El orden debe ser un número entero no negativo.";
        } elseif ($estado !== 0 && $estado !== 1) {
            $mensajeError = "El estado seleccionado no es válido.";
        } elseif (!empty($imagen)) {
            // Validar formato y tamaño de la imagen
            $extensionesPermitidas = ["jpg", "jpeg", "png", "gif"];
            $extensionArchivo = strtolower(pathinfo($imagen, PATHINFO_EXTENSION));
            if (!in_array($extensionArchivo, $extensionesPermitidas)) {
                $mensajeError = "El formato de la imagen no es válido. Use JPG, JPEG, PNG o GIF.";
            } elseif ($pesoImagen > 3145728) {
                $mensajeError = "El tamaño de la imagen no debe superar los 3 MB.";
            }
        }

        if (!isset($mensajeError)) {
            // Actualizar datos del banner
            $sql = "UPDATE banners SET titulo = :titulo, enfasis = :enfasis, descripcion = :descripcion, orden = :orden, estado = :estado WHERE id_banner = :id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":titulo", $titulo);
            $sentencia->bindParam(":enfasis", $enfasis);
            $sentencia->bindParam(":descripcion", $descripcion);
            $sentencia->bindParam(":orden", $orden);
            $sentencia->bindParam(":estado", $estado);
            $sentencia->bindParam(":id", $txtID);
            $sentencia->execute();

            // Verificar si se subió una nueva imagen
            if (!empty($imagen)) {
                // Eliminar la imagen anterior si existe
                if (!empty($banner["imagen"])) {
                    unlink("../../../images/banners/" . $banner["imagen"]);
                }

                // Subir la nueva imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];

                if ($tmpImagen != "") {
                    move_uploaded_file($tmpImagen, "../../../images/banners/" . $nombreArchivo);

                    // Actualizar la imagen en la base de datos
                    $sql = "UPDATE banners SET imagen = :imagen WHERE id_banner = :id";
                    $sentencia = $conexion->prepare($sql);
                    $sentencia->bindParam(":imagen", $nombreArchivo);
                    $sentencia->bindParam(":id", $txtID);
                    $sentencia->execute();
                }
            }
            header("Location: index.php");
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-image-line"></i>
            Editar Banner: <span class="text_red">&nbsp; N° <?php echo $banner['id_banner']; ?> ~ <?php echo $banner['pagina']; ?></span>
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

        <form id="form_banner" action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="titulo" class="form-label">
                        <i class="ri-price-tag-3-fill"></i> Título
                    </label>
                    <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Escriba el título" value="<?php echo $banner['titulo']; ?>" maxlength="100" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="enfasis" class="form-label">
                        <i class="ri-star-line"></i> Énfasis
                    </label>
                    <input type="text" class="form-control" name="enfasis" id="enfasis" placeholder="Escriba el énfasis" value="<?php echo $banner['enfasis']; ?>" maxlength="50" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-pencil-line"></i> Descripción (Opcional)
                    </label>
                    <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Escriba una descripción breve" maxlength="255"><?php echo $banner['descripcion']; ?></textarea>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="orden" class="form-label">
                        <i class="ri-sort-asc"></i> Orden
                    </label>
                    <input type="number" class="form-control" name="orden" id="orden" placeholder="Escriba el orden" value="<?php echo $banner['orden']; ?>" min="0" max="999" />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="estado" class="form-label">
                        <i class="ri-checkbox-multiple-line"></i> Estado
                    </label>
                    <select class="form-select" name="estado" id="estado" required>
                        <option value="1" <?php echo ($banner['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($banner['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-line"></i> Imagen (Máx. 3 MB)
                    </label>
                    <input type="file" class="form-control" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.gif" onchange="previewImage(event)" />
                </div>

                <div class="col-md-6 col-12 mb-3 d-flex flex-column">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-2-line"></i> Previsualización de la Imagen:
                    </label>
                    <img id="preview" src="../../../images/banners/<?php echo $banner['imagen']; ?>" alt="Vista previa de la imagen" class="img-fluid rounded border w-25" style="cursor: pointer; transition: width 0.5s ease;" onclick="toggleImageSize()" />
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
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function toggleImageSize() {
        const img = document.getElementById('preview');
        img.style.width = img.style.width === '100%' ? '25%' : '100%';
    }
</script>

<?php include("../../templates/footer_admin.php"); ?>
