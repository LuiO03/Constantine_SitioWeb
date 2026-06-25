<?php 
include("../inicio/conexion.php");

//============== OBTENER ID DEL BLOG A EDITAR ==============//
$txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

//============== CONSULTA PARA OBTENER DATOS DEL BLOG ==============//
$sql = "SELECT * FROM blogs WHERE id_blog = :id";
$sentencia = $conexion->prepare($sql);
$sentencia->bindParam(":id", $txtID);
$sentencia->execute();
$blog = $sentencia->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    // Captura de los datos editados del formulario
    $titulo_blog = isset($_POST["titulo_blog"]) ? $_POST["titulo_blog"] : "";
    $resumen = isset($_POST["resumen"]) ? $_POST["resumen"] : "";
    $contenido = isset($_POST["contenido"]) ? $_POST["contenido"] : "";
    $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
    $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;
    $enlace = isset($_POST["enlace"]) ? $_POST["enlace"] : "";

    if ($pesoImagen > 3145728) {
        $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
    } else if ($titulo_blog != "" && $resumen != "" && $contenido != "" && $enlace != "") {
        // Actualizar los datos del blog
        $sql = "UPDATE blogs SET 
            titulo_blog = :titulo_blog, 
            resumen = :resumen, 
            contenido = :contenido, 
            enlace = :enlace 
            WHERE id_blog = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":titulo_blog", $titulo_blog);
        $sentencia->bindParam(":resumen", $resumen);
        $sentencia->bindParam(":contenido", $contenido);
        $sentencia->bindParam(":enlace", $enlace);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        // Verificar si se subió una nueva imagen
        if ($imagen != "") {
            // Eliminar la imagen anterior si existe
            if ($blog["imagen"] != "") {
                unlink("../../../images/blogs/" . $blog["imagen"]);
            }

            // Subir la nueva imagen
            $fecha = new DateTime();
            $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
            $tmpImagen = $_FILES["imagen"]["tmp_name"];

            if ($tmpImagen != "") {
                move_uploaded_file($tmpImagen, "../../../images/blogs/" . $nombreArchivo);

                // Actualizar la imagen en la base de datos
                $sql = "UPDATE blogs SET imagen = :imagen WHERE id_blog = :id";
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindParam(":imagen", $nombreArchivo);
                $sentencia->bindParam(":id", $txtID);
                $sentencia->execute();
            }
        }
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
            <i class="ri-edit-box-line"></i>
            Editar Blog: <span class="text_red">&nbsp; <?php echo $blog['titulo_blog']; ?></span>
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

        <form id="form_blog" action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 mb-3">
                    <label for="titulo_blog" class="form-label">
                        <i class="ri-heading"></i> Título del Blog
                    </label>
                    <input type="text" class="form-control" name="titulo_blog" id="titulo_blog" placeholder="Título del blog" value="<?php echo $blog['titulo_blog']; ?>" required minlength="5" maxlength="100" pattern="[a-zA-Z0-9\s]+" />
                </div>

                <div class="col-12 mb-3">
                    <label for="resumen" class="form-label"><i class="ri-align-left"></i> Resumen</label>
                    <textarea class="form-control" name="resumen" id="resumen" rows="2" placeholder="Resumen del blog" required minlength="20" maxlength="300"><?php echo $blog['resumen']; ?></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label for="contenido" class="form-label">
                        <i class="ri-file-text-line"></i> Contenido
                    </label>
                    <textarea class="form-control" name="contenido" id="contenido" rows="8" placeholder="Escribe el contenido del blog aquí" required><?php echo $blog['contenido']; ?></textarea>
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
                    <img id="preview" src="../../../images/blogs/<?php echo $blog['imagen']; ?>" alt="Vista previa de la imagen" class="img-fluid rounded border w-25" style="cursor: pointer; transition: width 0.5s ease;" onclick="toggleImageSize()" />
                </div>

                <div class="col-12 mb-3">
                    <label for="enlace" class="form-label">
                        <i class="ri-link"></i> Enlace del Blog
                    </label>
                    <input type="url" class="form-control" name="enlace" id="enlace" placeholder="Enlace del blog" value="<?php echo $blog['enlace']; ?>" required pattern="https?://.*" />
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
        <small>Gestiona tus blogs de forma eficiente</small>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.25.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('contenido');
</script>

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
