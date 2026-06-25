<?php 
    include("../inicio/conexion.php");

    // Obtener categorías y público para cargar en los combobox
    $sqlCategorias = "SELECT id_categoria, nombre_categoria FROM categorias";
    $sentenciaCategorias = $conexion->prepare($sqlCategorias);
    $sentenciaCategorias->execute();
    $categorias = $sentenciaCategorias->fetchAll(PDO::FETCH_ASSOC);

    $sqlPublico = "SELECT id_publico, nombre_publico FROM publico";
    $sentenciaPublico = $conexion->prepare($sqlPublico);
    $sentenciaPublico->execute();
    $publicos = $sentenciaPublico->fetchAll(PDO::FETCH_ASSOC);

    

    if ($_POST) {
        $nombre_producto = isset($_POST["nombre_producto"]) ? $_POST["nombre_producto"] : "";
        $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
        $id_categoria = isset($_POST["id_categoria"]) ? $_POST["id_categoria"] : "";
        $id_publico = isset($_POST["id_publico"]) ? $_POST["id_publico"] : "";
        $precio_venta = isset($_POST["precio_venta"]) ? $_POST["precio_venta"] : "";
        $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
        $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

        // Función para eliminar tildes de una cadena
        function quitar_tildes($cadena) {
            $busca = array('á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ä', 'ë', 'ï', 'ö', 'ü', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü');
            $reemplaza = array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U');
            return str_replace($busca, $reemplaza, $cadena);
        }

        // Obtener el nombre de la categoría seleccionada
        $nombre_categoria = '';
        foreach ($categorias as $categoria) {
            if ($categoria['id_categoria'] == $id_categoria) {
                $nombre_categoria = $categoria['nombre_categoria'];
                break;
            }
        }

        // Eliminar tildes y convertir a mayúsculas
        $nombre_categoria_sin_tildes = strtoupper(quitar_tildes($nombre_categoria));

        // Generar el código de producto sin tildes y en mayúsculas
        $codigo_producto = substr($nombre_categoria_sin_tildes, 0, 3) . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);


        // Obtener el nombre del público seleccionado
        $nombre_publico = '';
        foreach ($publicos as $publico) {
            if ($publico['id_publico'] == $id_publico) {
                $nombre_publico = $publico['nombre_publico'];
                break;
            }
        }

        // Si se ha subido una imagen
        if ($imagen != "") {
            if ($pesoImagen > 3145728) {
                $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
            } else {
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];

                // Definir la ruta con el nombre del público
                $rutaDestino = "../../../images/productos/" . $nombre_publico . "/" . $nombreArchivo;

                if ($tmpImagen != "") {
                    // Crear directorio para el público si no existe
                    if (!file_exists("../../../images/productos/" . $nombre_publico)) {
                        mkdir("../../../images/productos/" . $nombre_publico, 0777, true);
                    }

                    // Mover la imagen al directorio adecuado
                    move_uploaded_file($tmpImagen, $rutaDestino);
                }
            }
        } else {
            $mensajeError = "Debe subir una imagen.";
        }

        // Insertar datos solo si los campos están completos y no hay error en la imagen
        if (!isset($mensajeError) && $nombre_producto != "" && $descripcion != "" && $id_categoria != "" && $id_publico != "" && $precio_venta != "" && isset($nombreArchivo)) {
            $sql = "INSERT INTO productos (codigo_producto, nombre_producto, descripcion, id_categoria, id_publico, precio_venta, imagen)
            VALUES (:codigo_producto, :nombre_producto, :descripcion, :id_categoria, :id_publico, :precio_venta, :imagen)";

            $sentencia = $conexion->prepare($sql);

            $sentencia->bindParam(":codigo_producto", $codigo_producto);
            $sentencia->bindParam(":nombre_producto", $nombre_producto);
            $sentencia->bindParam(":descripcion", $descripcion);
            $sentencia->bindParam(":id_categoria", $id_categoria);
            $sentencia->bindParam(":id_publico", $id_publico);
            $sentencia->bindParam(":precio_venta", $precio_venta);
            $sentencia->bindParam(":imagen", $nombreArchivo);

            $sentencia->execute();

            // Obtener el ID del producto recién creado
            $id_producto = $conexion->lastInsertId();

            // Redirigir a variantes.php con el ID del producto
            header("Location: variantes.php?txtID=$id_producto");
        } else if (!isset($mensajeError)) {
            $mensajeError = "Todos los campos son obligatorios, incluyendo la imagen.";
        }
    }

    include("../../templates/header_admin.php");
?>




<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">

    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-shirt-fill"></i>
            Agregar<span class="text_red"> Producto</span>
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

        <form id="form_producto" action="" method="post" enctype="multipart/form-data">
            <div class="row">

                <div class="col-md-6 col-12 mb-3">
                    <label for="nombre_producto" class="form-label">
                        <i class="ri-shopping-bag-3-fill"></i> Nombre del Producto
                    </label>
                    <input type="text" class="form-control" name="nombre_producto" id="nombre_producto" placeholder="Escriba el nombre del producto" autofocus required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-file-text-line"></i> Descripción del Producto
                    </label>
                    <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Escriba la descripción del producto" rows="4" required></textarea>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="id_categoria" class="form-label">
                        <i class="ri-price-tag-3-fill"></i> Categoría
                    </label>
                    <select class="form-control" name="id_categoria" id="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria) { ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>"><?php echo $categoria['nombre_categoria']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="id_publico" class="form-label">
                        <i class="ri-user-3-fill"></i> Público
                    </label>
                    <select class="form-control" name="id_publico" id="id_publico" required>
                        <option value="">Seleccione un público</option>
                        <?php foreach ($publicos as $publico) { ?>
                            <option value="<?php echo $publico['id_publico']; ?>"><?php echo $publico['nombre_publico']; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="precio_venta" class="form-label">
                        <i class="ri-money-dollar-circle-fill"></i> Precio de Venta
                    </label>
                    <input type="number" step="0.01" class="form-control" name="precio_venta" id="precio_venta" placeholder="Ingrese el precio de venta" required />
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

            <div class="d-flex justify-content-end gap-3">

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-arrow-right-fill"></i> Siguiente
                </button>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus productos de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
