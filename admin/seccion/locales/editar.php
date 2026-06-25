<?php 
include("../inicio/conexion.php");

//============== OBTENER ID DEL LOCAL A EDITAR ==============//
$txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

//============== CONSULTA PARA OBTENER DATOS DEL LOCAL ==============//
$sql = "SELECT * FROM locales WHERE id_local = :id";
$sentencia = $conexion->prepare($sql);
$sentencia->bindParam(":id", $txtID);
$sentencia->execute();
$local = $sentencia->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    // Captura de los datos editados del formulario
    $nombre_local = isset($_POST["nombre_local"]) ? htmlspecialchars($_POST["nombre_local"]) : "";
    $direccion = isset($_POST["direccion"]) ? htmlspecialchars($_POST["direccion"]) : "";
    $horario = isset($_POST["horario"]) ? htmlspecialchars($_POST["horario"]) : "";
    $telefono = isset($_POST["telefono"]) ? htmlspecialchars($_POST["telefono"]) : "";
    $enlace = isset($_POST["enlace"]) ? htmlspecialchars($_POST["enlace"]) : "";
    $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
    $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

    // Validaciones
    if ($pesoImagen > 3145728) {
        $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
    } else if ($nombre_local != "" && $direccion != "" && $horario != "" && $enlace != "") {
        // Validación de enlace (URL válida)
        if (!filter_var($enlace, FILTER_VALIDATE_URL)) {
            $mensajeError = "El enlace de Google Maps no es válido.";
        } else {
            // Actualizar los datos del local
            $sql = "UPDATE locales SET 
                nombre_local = :nombre_local, 
                direccion = :direccion, 
                horario = :horario, 
                telefono = :telefono, 
                enlace = :enlace 
                WHERE id_local = :id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":nombre_local", $nombre_local);
            $sentencia->bindParam(":direccion", $direccion);
            $sentencia->bindParam(":horario", $horario);
            $sentencia->bindParam(":telefono", $telefono);
            $sentencia->bindParam(":enlace", $enlace);
            $sentencia->bindParam(":id", $txtID);
            $sentencia->execute();

            // Verificar si se subió una nueva imagen
            if ($imagen != "") {
                // Eliminar la imagen anterior si existe
                if ($local["imagen"] != "") {
                    unlink("../../../images/locales/" . $local["imagen"]);
                }

                // Verificación del tipo de archivo
                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
                $extensionImagen = pathinfo($imagen, PATHINFO_EXTENSION);
                if (!in_array(strtolower($extensionImagen), $extensionesPermitidas)) {
                    $mensajeError = "Formato de imagen no permitido. Solo se permiten JPG, PNG o GIF.";
                } else {
                    // Subir la nueva imagen
                    $fecha = new DateTime();
                    $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                    $tmpImagen = $_FILES["imagen"]["tmp_name"];

                    if ($tmpImagen != "") {
                        move_uploaded_file($tmpImagen, "../../../images/locales/" . $nombreArchivo);

                        // Actualizar la imagen en la base de datos
                        $sql = "UPDATE locales SET imagen = :imagen WHERE id_local = :id";
                        $sentencia = $conexion->prepare($sql);
                        $sentencia->bindParam(":imagen", $nombreArchivo);
                        $sentencia->bindParam(":id", $txtID);
                        $sentencia->execute();
                    }
                }
            }
            header("Location:index.php");
        }
    } else {
        $mensajeError = "Todos los campos son obligatorios.";
    }
}

include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Editar Local: <span class="text_red">&nbsp; N° <?php echo $local['id_local']; ?> ~ <?php echo $local['nombre_local']; ?></span>
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
    <!-- Form Fields -->
    <div class="row">
        <div class="col-md-6 col-12 mb-3">
            <label for="nombre_local" class="form-label">
                <i class="ri-price-tag-3-fill"></i> Nombre del Local
            </label>
            <input type="text" class="form-control" name="nombre_local" id="nombre_local" placeholder="Nombre del local" value="<?php echo $local['nombre_local']; ?>" required maxlength="100" />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="direccion" class="form-label"><i class="ri-align-left"></i> Dirección</label>
            <textarea class="form-control" name="direccion" id="direccion" rows="1" placeholder="Dirección del local" required maxlength="200"><?php echo $local['direccion']; ?></textarea>
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="horario" class="form-label"><i class="ri-time-line"></i> Horario</label>
            <textarea class="form-control" name="horario" id="horario" rows="1" placeholder="Horario del local" required maxlength="100"><?php echo $local['horario']; ?></textarea>
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="telefono" class="form-label"><i class="ri-phone-line"></i> Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="telefono" placeholder="Teléfono del local" value="<?php echo $local['telefono']; ?>" pattern="^\+?[0-9]{7,15}$" title="El teléfono debe contener solo números y un máximo de 15 dígitos" maxlength="15" />
        </div>

        <div class="col-12 mb-3">
            <label for="enlace" class="form-label">
                <i class="ri-link"></i> Enlace de Google Maps
            </label>
            <input type="url" class="form-control" name="enlace" id="enlace" placeholder="Enlace del local en Google Maps" value="<?php echo $local['enlace']; ?>" required pattern="https?://.*" title="Debe ser una URL válida que comience con http:// o https://" maxlength="255" />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="imagen" class="form-label">
                <i class="ri-image-line"></i> Imagen (Máx. 3 MB)
            </label>
            <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*" onchange="previewImage(event)" />
        </div>

        <div class="col-md-6 col-12 mb-3 d-flex flex-column">
            <label for="imagen" class="form-label">
                <i class="ri-image-2-line"></i> Previsualización de la imagen:
            </label>
            <img id="preview" src="../../../images/locales/<?php echo $local['imagen']; ?>" alt="Vista previa de la imagen" class="img-fluid rounded border w-25" style="cursor: pointer; transition: width 0.5s ease;" onclick="toggleImageSize()" />
        </div>
    </div>

    <div class="d-flex justify-content-start gap-3">
        <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
            <i class="ri-save-3-fill"></i> Guardar cambios
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

<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleImageSize() {
        const preview = document.getElementById('preview');
        if (preview.style.width === "100%") {
            preview.style.width = "25%";
        } else {
            preview.style.width = "100%";
        }
    }
</script>

<?php include("../../templates/footer_admin.php"); ?>
