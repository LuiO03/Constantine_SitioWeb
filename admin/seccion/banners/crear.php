<?php 
    
    include("../inicio/conexion.php");
    
    if ($_POST) {
        
        // =========== POST TITULO =========== //

        // Verificar si el campo "titulo" está presente en el array $_POST
        if (isset($_POST["titulo"])) {
            $titulo = $_POST["titulo"]; // Asignar el valor del campo "titulo"
        } else {
            $titulo = ""; // Asignar una cadena vacía si no está presente
        }

        // =========== POST DESCRIPCIÓN =========== //
        if (isset($_POST["descripcion"])) {
            $descripcion = $_POST["descripcion"];
        } else {
            $descripcion = "";
        }

        // =========== POST ENLACE =========== //
        if (isset($_POST["link"])) {
            $link = $_POST["link"];
        } else {
            $link = "";
        }

        /*  CODIGO MÁS RESUMIDO
        $titulo = isset($_POST["titulo"]) ? $_POST["titulo"] : "";
        $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
        $link = isset($_POST["link"]) ? $_POST["link"] : "";
        */
        
        // =========== CONSULTA PARA INSERTAR DATOS =========== //
        $sql = "INSERT INTO `tbl_banners` (`ID`, `titulo`, `descripcion`, `link`) VALUES (NULL, :titulo, :descripcion, :link)";
        $sentencia=$conexion->prepare($sql);
    
        $sentencia->bindParam(":titulo",$titulo);
        $sentencia->bindParam(":descripcion",$descripcion);
        $sentencia->bindParam(":link",$link);

        $sentencia->execute();
        header("Location:index.php");
    }

    include("../../templates/header_admin.php");
?>


<div class="card shadow-sm mt-4 w-80 mx-auto">

    <div class="card-header d-flex justify-content-between align-items-center" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-image-2-fill"></i>
            Agregar <span class="text_red">Banner</span>
        </span>

        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="index.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Atras
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <form action="" method="post">
            <div class="mb-3">
                <label for="titulo" class="form-label"> 
                    <i class="ri-pencil-line"></i> Título
                </label>
                <input type="text" class="form-control" name="titulo" id="titulo" placeholder="Escriba el título del banner" autofocus/>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label"> 
                    <i class="ri-align-left"></i> Descripción
                </label>
                <input type="text" class="form-control" name="descripcion" id="descripcion" placeholder="Escriba los detalles aquí" />
            </div>

            <div class="mb-3">
                <label for="link" class="form-label"> 
                    <i class="ri-link"></i> Enlace
                </label>
                <input type="text" class="form-control" name="link" id="link" placeholder="Escriba aquí el link" />
            </div>

            <div class="d-flex justify-content-start gap-3">
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-add-circle-line"></i> Crear Banner
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus banners de forma eficiente</small>
    </div>
</div>


<?php include("../../templates/footer_admin.php"); ?>