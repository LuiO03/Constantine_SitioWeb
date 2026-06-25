<?php 
include("../inicio/conexion.php");

if ($_POST) {
    // Obtener los datos del formulario
    $titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : "";
    $contenido = isset($_POST["contenido"]) ? $_POST["contenido"] : "";
    $autor = isset($_POST["autor"]) ? $_POST["autor"] : "";
    $etiquetas = isset($_POST["etiquetas"]) ? $_POST["etiquetas"] : "";
    $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
    $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

    // Validar campos con pattern y longitudes
    $patternTitulo = "/^[a-zA-Z0-9\s]+$/"; // Solo letras, números y espacios
    $patternAutor = "/^[a-zA-Z\s]+$/"; // Solo letras y espacios

    if (!preg_match($patternTitulo, $titulo)) {
        $mensajeError = "El título solo puede contener letras, números y espacios.";
    } elseif (strlen($titulo) < 5 || strlen($titulo) > 100) {
        $mensajeError = "El título debe tener entre 5 y 100 caracteres.";
    } elseif (strlen($contenido) < 10) {
        $mensajeError = "El contenido debe tener al menos 10 caracteres.";
    } elseif (!preg_match($patternAutor, $autor)) {
        $mensajeError = "El autor solo puede contener letras y espacios.";
    } elseif (strlen($autor) < 3 || strlen($autor) > 50) {
        $mensajeError = "El autor debe tener entre 3 y 50 caracteres.";
    }

    // Validar si se ha subido una imagen
    if ($imagen != "") {
        // Validar tamaño de la imagen (máximo 3MB)
        if ($pesoImagen > 3145728) { // 3MB en bytes
            $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
        } else {
            // Validar tipo de imagen (solo jpg, jpeg, png, gif)
            $extImagen = strtolower(pathinfo($imagen, PATHINFO_EXTENSION));
            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($extImagen, $tiposPermitidos)) {
                $mensajeError = "Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
            } else {
                // Generar un nombre único para la imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];

                if ($tmpImagen != "") {
                    // Subir la imagen a la carpeta correcta
                    move_uploaded_file($tmpImagen, "../../../images/blogs/" . $nombreArchivo);
                }
            }
        }
    } else {
        $mensajeError = "Debe seleccionar una imagen destacada para el blog.";
    }

    // Validar campos obligatorios antes de insertar en la base de datos
    if (!isset($mensajeError) && $titulo != "" && $contenido != "" && $autor != "") {
        // Consulta para insertar los datos en la base de datos
        $sql = "INSERT INTO blogs (titulo, contenido, autor, etiquetas, imagen, fecha_creacion, fecha_actualizacion) 
                VALUES (:titulo, :contenido, :autor, :etiquetas, :imagen, NOW(), NOW())";
        $sentencia = $conexion->prepare($sql);

        $sentencia->bindParam(":titulo", $titulo);
        $sentencia->bindParam(":contenido", $contenido);
        $sentencia->bindParam(":autor", $autor);
        $sentencia->bindParam(":etiquetas", $etiquetas);
        $sentencia->bindParam(":imagen", $nombreArchivo);

        $sentencia->execute();

        // Redirigir al listado de blogs tras la inserción
        header("Location: index.php");
    } else if (!isset($mensajeError)) {
        $mensajeError = "Todos los campos son obligatorios, incluyendo la imagen destacada.";
    }
}

include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-article-line"></i>
            Crear <span class="text_red">Blog</span>
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

        <form id="form_blog" action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 col-12 mb-3">
                    <label for="titulo" class="form-label">
                        <i class="ri-article-line"></i> Título del Blog
                    </label>
                    <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Escriba el título del blog" pattern="[a-zA-Z0-9\s]+" minlength="5" maxlength="100" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="autor" class="form-label">
                        <i class="ri-user-line"></i> Autor
                    </label>
                    <input type="text" class="form-control" name="autor" id="autor" placeholder="Escriba el autor del blog" pattern="[a-zA-Z\s]+" minlength="3" maxlength="50" required />
                </div>

                <div class="col-12 mb-3">
                    <label for="contenido" class="form-label">
                        <i class="ri-file-text-line"></i> Contenido
                    </label>
                    <textarea class="form-control" name="contenido" id="contenido" rows="10" placeholder="Escriba el contenido del blog" minlength="10" required></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label for="etiquetas" class="form-label">
                        <i class="ri-price-tag-3-line"></i> Etiquetas (separadas por comas)
                    </label>
                    <input type="text" class="form-control" name="etiquetas" id="etiquetas" placeholder="Ejemplo: tecnología, programación, tutorial" />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-line"></i> Imagen Destacada
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
                    <i class="ri-add-circle-line"></i> Crear Blog
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Publica tus blogs y comparte conocimiento</small>
    </div>
</div>

<script>
    // Inicialización de TinyMCE
    tinymce.init({
        selector: '#contenido',
        height: 400,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image',
        file_picker_callback: function(callback, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.onchange = function() {
                var file = input.files[0];
                var reader = new FileReader();
                reader.onload = function() {
                    callback(reader.result, {
                        alt: file.name
                    });
                };
                reader.readAsDataURL(file);
            };
            input.click();
        }
    });

    // Previsualizar imagen
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Cambiar tamaño de imagen al hacer clic
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
