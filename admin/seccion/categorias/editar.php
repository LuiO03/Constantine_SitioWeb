<?php 
    include("../inicio/conexion.php");

    //============== OBTENER ID DE LA CATEGORÍA A EDITAR ==============//
    $txtID = isset($_GET['txtID']) ? intval($_GET['txtID']) : "";

    //============== CONSULTA PARA OBTENER DATOS DE LA CATEGORÍA ==============//
    $sql = "SELECT * FROM categorias WHERE id_categoria = :id";
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindParam(":id", $txtID, PDO::PARAM_INT);
    $sentencia->execute();
    $categoria = $sentencia->fetch(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura de los datos editados del formulario y sanitización
        $nombre_categoria = isset($_POST["nombre_categoria"]) ? htmlspecialchars(trim($_POST["nombre_categoria"])) : "";
        $ubicacion = isset($_POST["ubicacion"]) ? htmlspecialchars(trim($_POST["ubicacion"])) : "";
        $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
        $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

        // Validaciones del lado del servidor
        if (empty($nombre_categoria) || strlen($nombre_categoria) < 3 || strlen($nombre_categoria) > 30) {
            $mensajeError = "El nombre de la categoría debe tener entre 3 y 30 caracteres.";
        } elseif (empty($ubicacion) || strlen($ubicacion) < 3 || strlen($ubicacion) > 100) {
            $mensajeError = "La ubicación debe tener entre 3 y 100 caracteres.";
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
            // Actualizar los datos de la categoría
            $sql = "UPDATE categorias SET nombre_categoria = :nombre_categoria, ubicacion = :ubicacion WHERE id_categoria = :id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":nombre_categoria", $nombre_categoria, PDO::PARAM_STR);
            $sentencia->bindParam(":ubicacion", $ubicacion, PDO::PARAM_STR);
            $sentencia->bindParam(":id", $txtID, PDO::PARAM_INT);
            $sentencia->execute();

            // Verificar si se subió una nueva imagen
            if ($imagen != "") {
                // Eliminar la imagen anterior si existe
                if ($categoria["imagen"] != "") {
                    unlink("../../../images/categorias/" . $categoria["imagen"]);
                }

                // Subir la nueva imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];

                if ($tmpImagen != "") {
                    move_uploaded_file($tmpImagen, "../../../images/categorias/" . $nombreArchivo);

                    // Actualizar la imagen en la base de datos
                    $sql = "UPDATE categorias SET imagen = :imagen WHERE id_categoria = :id";
                    $sentencia = $conexion->prepare($sql);
                    $sentencia->bindParam(":imagen", $nombreArchivo, PDO::PARAM_STR);
                    $sentencia->bindParam(":id", $txtID, PDO::PARAM_INT);
                    $sentencia->execute();
                }
            }
            header("Location:index.php");
        }
    }
    
    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Editar Categoría: <span class="text_red">&nbsp; N° <?php echo $categoria['id_categoria']; ?> ~ <?php echo $categoria['nombre_categoria']; ?></span>
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

        <form id="form_categoria" action="" method="post" enctype="multipart/form-data" novalidate>
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="nombre_categoria" class="form-label">
                        <i class="ri-price-tag-3-fill"></i> Nombre de Categoría
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="nombre_categoria" 
                        id="nombre_categoria" 
                        placeholder="Escriba el nombre de la categoría" 
                        value="<?php echo $categoria['nombre_categoria']; ?>" 
                        minlength="3" 
                        maxlength="30" 
                        pattern="[A-Za-z\s]+" 
                        required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="ubicacion" class="form-label">
                        <i class="ri-map-pin-line"></i> Ubicación
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="ubicacion" 
                        id="ubicacion" 
                        placeholder="Escriba la ubicación" 
                        value="<?php echo $categoria['ubicacion']; ?>" 
                        minlength="3" 
                        maxlength="100" 
                        required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-line"></i> Imagen (Máx. 3 MB)
                    </label>
                    <input 
                        type="file" 
                        class="form-control" 
                        name="imagen" 
                        id="imagen" 
                        accept=".jpg,.jpeg,.png,.gif" 
                        onchange="previewImage(event)" />
                </div>

                <div class="col-md-6 col-12 mb-3 d-flex flex-column">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-2-line"></i> Previsualización de la Imagen:
                    </label>
                    <img 
                        id="preview" 
                        src="../../../images/categorias/<?php echo $categoria['imagen']; ?>" 
                        alt="Vista previa de la imagen" 
                        class="img-fluid rounded border w-25" 
                        style="cursor: pointer; transition: width 0.5s ease;" 
                        onclick="toggleImageSize()" />
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
        <small>Gestiona tus categorías de forma eficiente</small>
    </div>
</div>

<script>
    // Previsualizar imagen seleccionada
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // Cambiar tamaño de previsualización al hacer clic
    function toggleImageSize() {
        const img = document.getElementById('preview');
        img.style.width = img.style.width === '100%' ? '25%' : '100%';
    }
</script>

<?php include("../../templates/footer_admin.php"); ?>
