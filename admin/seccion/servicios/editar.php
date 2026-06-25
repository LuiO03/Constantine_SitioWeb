<?php 
include("../inicio/conexion.php");

//============== OBTENER ID DEL SERVICIO A EDITAR ==============//
$txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

//============== CONSULTA PARA OBTENER DATOS DEL SERVICIO ==============//
$sql = "SELECT * FROM servicio WHERE id_servicio = :id";
$sentencia = $conexion->prepare($sql);
$sentencia->bindParam(":id", $txtID);
$sentencia->execute();
$servicio = $sentencia->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    // Captura de los datos editados del formulario
    $nombre_servicio = isset($_POST["nombre_servicio"]) ? $_POST["nombre_servicio"] : "";
    $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
    $detalle_1 = isset($_POST["detalle_1"]) ? $_POST["detalle_1"] : "";
    $detalle_2 = isset($_POST["detalle_2"]) ? $_POST["detalle_2"] : "";
    $detalle_3 = isset($_POST["detalle_3"]) ? $_POST["detalle_3"] : "";
    $frase = isset($_POST["frase"]) ? $_POST["frase"] : "";
    $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
    $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

    // Validaciones
    if ($pesoImagen > 3145728) {
        $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
    } else if ($nombre_servicio != "" && $descripcion != "") {
        // Validar imagen tipo
        $imageFileType = strtolower(pathinfo($imagen, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            $mensajeError = "Solo se permiten imágenes de tipo JPG, JPEG, PNG o GIF.";
        } else {
            try {
                // Actualizar los datos del servicio
                $sql = "UPDATE servicio SET 
                    nombre_servicio = :nombre_servicio, 
                    descripcion = :descripcion, 
                    detalle_1 = :detalle_1, 
                    detalle_2 = :detalle_2, 
                    detalle_3 = :detalle_3, 
                    frase = :frase 
                    WHERE id_servicio = :id";
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindParam(":nombre_servicio", $nombre_servicio);
                $sentencia->bindParam(":descripcion", $descripcion);
                $sentencia->bindParam(":detalle_1", $detalle_1);
                $sentencia->bindParam(":detalle_2", $detalle_2);
                $sentencia->bindParam(":detalle_3", $detalle_3);
                $sentencia->bindParam(":frase", $frase);
                $sentencia->bindParam(":id", $txtID);
                $sentencia->execute();

                // Verificar si se subió una nueva imagen
                if ($imagen != "") {
                    // Eliminar la imagen anterior si existe
                    if ($servicio["imagen"] != "") {
                        unlink("../../../images/servicios/" . $servicio["imagen"]);
                    }

                    // Subir la nueva imagen
                    $fecha = new DateTime();
                    $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                    $tmpImagen = $_FILES["imagen"]["tmp_name"];

                    if ($tmpImagen != "") {
                        move_uploaded_file($tmpImagen, "../../../images/servicios/" . $nombreArchivo);

                        // Actualizar la imagen en la base de datos
                        $sql = "UPDATE servicio SET imagen = :imagen WHERE id_servicio = :id";
                        $sentencia = $conexion->prepare($sql);
                        $sentencia->bindParam(":imagen", $nombreArchivo);
                        $sentencia->bindParam(":id", $txtID);
                        $sentencia->execute();
                    }
                }

                header("Location:index.php");
            } catch (PDOException $e) {
                $mensajeError = "Error al actualizar el servicio: " . $e->getMessage();
            }
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
            Editar Servicio: <span class="text_red">&nbsp; N° <?php echo $servicio['id_servicio']; ?> ~ <?php echo $servicio['nombre_servicio']; ?></span>
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
                    <input type="text" class="form-control" name="nombre_servicio" id="nombre_servicio" placeholder="Nombre del servicio" value="<?php echo $servicio['nombre_servicio']; ?>" required minlength="3" maxlength="100" pattern="[A-Za-z0-9 ]+" />
                </div>

                <div class="col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-align-left"></i> Descripción
                    </label>
                    <textarea class="form-control" name="descripcion" id="descripcion" rows="3" placeholder="Descripción del servicio" required minlength="10" maxlength="500"><?php echo $servicio['descripcion']; ?></textarea>
                </div>

                <div class="col-md-4 col-12 mb-3">
                    <label for="detalle_1" class="form-label"><i class="ri-list-unordered"></i> Detalle 1</label>
                    <textarea class="form-control" name="detalle_1" id="detalle_1" rows="4" placeholder="Primer detalle" maxlength="300"><?php echo $servicio['detalle_1']; ?></textarea>
                </div>

                <div class="col-md-4 col-12 mb-3">
                    <label for="detalle_2" class="form-label"><i class="ri-list-unordered"></i> Detalle 2</label>
                    <textarea class="form-control" name="detalle_2" id="detalle_2" rows="4" placeholder="Segundo detalle" maxlength="300"><?php echo $servicio['detalle_2']; ?></textarea>
                </div>

                <div class="col-md-4 col-12 mb-3">
                    <label for="detalle_3" class="form-label"><i class="ri-list-unordered"></i> Detalle 3</label>
                    <textarea class="form-control" name="detalle_3" id="detalle_3" rows="4" placeholder="Tercer detalle" maxlength="300"><?php echo $servicio['detalle_3']; ?></textarea>
                </div>

                <div class="col-12 mb-3">
                    <label for="frase" class="form-label">
                        <i class="ri-quotation-mark"></i> Frase
                    </label>
                    <input type="text" class="form-control" name="frase" id="frase" placeholder="Frase del servicio" value="<?php echo $servicio['frase']; ?>" maxlength="255" />
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
                    <img src="../../../images/servicios/<?php echo $servicio['imagen']; ?>" id="preview" alt="Imagen del servicio" class="img-fluid" style="max-height: 200px; object-fit: cover;" />
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-success w-100">
                    <i class="ri-save-3-line"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function () {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>


<?php include("../../templates/footer_admin.php"); ?>
