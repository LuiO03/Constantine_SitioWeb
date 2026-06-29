<?php 
    include("../inicio/conexion.php");

    //============== OBTENER ID DEL PRODUCTO A EDITAR ==============//
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

    //============== CONSULTA PARA OBTENER DATOS DEL PRODUCTO ==============//
    $sql = "SELECT p.*, pu.nombre_publico FROM productos p INNER JOIN publico pu ON p.id_publico = pu.id_publico WHERE p.id_producto = :id";
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $producto = $sentencia->fetch(PDO::FETCH_ASSOC);

    //============== CONSULTA PARA OBTENER CATEGORÍAS ==============//
    $sqlCategorias = "SELECT * FROM categorias";
    $sentenciaCategorias = $conexion->prepare($sqlCategorias);
    $sentenciaCategorias->execute();
    $categorias = $sentenciaCategorias->fetchAll(PDO::FETCH_ASSOC);

    //============== CONSULTA PARA OBTENER PÚBLICOS ==============//
    $sqlPublico = "SELECT * FROM publico";
    $sentenciaPublico = $conexion->prepare($sqlPublico);
    $sentenciaPublico->execute();
    $publicos = $sentenciaPublico->fetchAll(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura de los datos editados del formulario
        $nombre_producto = isset($_POST["nombre_producto"]) ? $_POST["nombre_producto"] : "";
        $codigo_producto = isset($_POST["codigo_producto"]) ? $_POST["codigo_producto"] : "";
        $stock_total = isset($_POST["stock_total"]) ? $_POST["stock_total"] : "";
        $precio_venta = isset($_POST["precio_venta"]) ? $_POST["precio_venta"] : "";
        $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
        $id_categoria = isset($_POST["id_categoria"]) ? $_POST["id_categoria"] : "";
        $id_publico = isset($_POST["id_publico"]) ? $_POST["id_publico"] : "";
        $nombre_publico = isset($_POST["nombre_publico"]) ? $_POST["nombre_publico"] : "";
        $imagen = isset($_FILES["imagen"]["name"]) ? $_FILES["imagen"]["name"] : "";
        $pesoImagen = isset($_FILES["imagen"]["size"]) ? $_FILES["imagen"]["size"] : 0;

        // Verificar si se ha seleccionado una categoría
        if ($id_categoria != "") {
            // Obtener las primeras tres letras de la categoría seleccionada
            $codigo_categoria = substr($categorias[$id_categoria - 1]['nombre_categoria'], 0, 3);
            
            // Obtener el último producto para esa categoría para generar un código único
            $sqlCodigo = "SELECT MAX(CAST(SUBSTRING(codigo_producto, 4) AS UNSIGNED)) AS max_codigo FROM productos WHERE id_categoria = :id_categoria";
            $sentenciaCodigo = $conexion->prepare($sqlCodigo);
            $sentenciaCodigo->bindParam(":id_categoria", $id_categoria);
            $sentenciaCodigo->execute();
            $codigoProductoExistente = $sentenciaCodigo->fetch(PDO::FETCH_ASSOC);
            $numeroProducto = $codigoProductoExistente['max_codigo'] + 1;

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
        }

        if ($pesoImagen > 3145728) {
            $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
        } else if ($nombre_producto != "" && $codigo_producto != "" && $stock_total != "" && $precio_venta != "" && $descripcion != "" && $id_categoria != "" && $id_publico != "") {
            // Actualizar los datos del producto
            $sql = "UPDATE productos SET nombre_producto = :nombre_producto, codigo_producto = :codigo_producto, stock_total = :stock_total, precio_venta = :precio_venta, descripcion = :descripcion, id_categoria = :id_categoria, id_publico = :id_publico WHERE id_producto = :id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":nombre_producto", $nombre_producto);
            $sentencia->bindParam(":codigo_producto", $codigo_producto);
            $sentencia->bindParam(":stock_total", $stock_total);
            $sentencia->bindParam(":precio_venta", $precio_venta);
            $sentencia->bindParam(":descripcion", $descripcion);
            $sentencia->bindParam(":id_categoria", $id_categoria);
            $sentencia->bindParam(":id_publico", $id_publico);
            $sentencia->bindParam(":id", $txtID);
            $sentencia->execute();

            // Verificar si se subió una nueva imagen
            if ($imagen != "") {
                // Eliminar la imagen anterior si existe
                if ($producto["imagen"] != "") {
                    unlink("../../../images/productos/" . $producto["imagen"]);
                }

                // Subir la nueva imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $imagen;
                $tmpImagen = $_FILES["imagen"]["tmp_name"];

                if ($tmpImagen != "") {
                    move_uploaded_file($tmpImagen, "../../../images/productos/" . $nombreArchivo);

                    // Actualizar la imagen en la base de datos
                    $sql = "UPDATE productos SET imagen = :imagen WHERE id_producto = :id";
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
            <i class="ri-shirt-fill"></i>
            Editar Producto: <span class="text_red">&nbsp; N° <?php echo $producto['id_producto']; ?> ~ <?php echo $producto['nombre_producto']; ?></span>
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
                        <i class="ri-shirt-fill"></i> Nombre de Producto
                    </label>
                    <input type="text" class="form-control" name="nombre_producto" id="nombre_producto" placeholder="Escriba el nombre del producto" value="<?php echo $producto['nombre_producto']; ?>" autofocus required />
                </div>

                <div class="col-12 mb-3">
                    <label for="descripcion" class="form-label">
                        <i class="ri-file-text-line"></i> Descripción del Producto
                    </label>
                    <textarea class="form-control" name="descripcion" id="descripcion" placeholder="Escriba la descripción del producto" rows="4" required><?php echo $producto['descripcion']; ?></textarea>
                </div>


                <div class="col-md-6 col-12 mb-3">
                    <label for="stock_total" class="form-label">
                        <i class="ri-drop-line"></i> Stock Total
                    </label>
                    <input type="number" class="form-control" name="stock_total" id="stock_total" placeholder="Cantidad Total del Stock" value="<?php echo $producto['stock_total']; ?>" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="precio_venta" class="form-label">
                        <i class="ri-money-dollar-circle-line"></i> Precio de Venta
                    </label>
                    <input type="text" class="form-control" name="precio_venta" id="precio_venta" placeholder="Precio de venta" value="<?php echo $producto['precio_venta']; ?>" required />
                </div>

                <!-- Combobox para Categoría -->
                <div class="col-md-6 col-12 mb-3">
                    <label for="id_categoria" class="form-label">
                        <i class="ri-price-tag-3-line"></i> Categoría
                    </label>
                    <select class="form-control" name="id_categoria" id="id_categoria" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria) { ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($producto['id_categoria'] == $categoria['id_categoria']) ? "selected" : ""; ?>>
                                <?php echo $categoria['nombre_categoria']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Combobox para Público -->
                <div class="col-md-6 col-12 mb-3">
                    <label for="id_publico" class="form-label">
                        <i class="ri-group-line"></i> Público
                    </label>
                    <select class="form-control" name="id_publico" id="id_publico" required>
                        <option value="">Seleccione un público</option>
                        <?php foreach ($publicos as $publico) { ?>
                            <option value="<?php echo $publico['id_publico']; ?>" <?php echo ($producto['id_publico'] == $publico['id_publico']) ? "selected" : ""; ?>>
                                <?php echo $publico['nombre_publico']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-line"></i> Imagen (Máx. 3 MB)
                    </label>
                    <input type="file" class="form-control" name="imagen" id="imagen" accept="image/*" onchange="previewImage(event)" />
                </div>

                <div class="col-md-6 col-12 mb-3 d-flex flex-column">
                    <label for="imagen" class="form-label">
                        <i class="ri-image-2-line"></i> Previsualización de la Imagen:
                    </label>
                    <img id="preview" src="<?php echo $url_base; ?>images/productos/<?php echo strtolower(htmlspecialchars($producto['nombre_publico'])); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Vista previa de la imagen" class="img-fluid rounded border w-25" style="cursor: pointer; transition: width 0.5s ease;" onclick="toggleImageSize()" />
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
        <small>Gestiona tus productos de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
