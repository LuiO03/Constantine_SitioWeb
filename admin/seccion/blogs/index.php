<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la imagen
        $sentencia = $conexion->prepare("SELECT * FROM blogs WHERE id_blog = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $registro_imagen = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar la imagen del servidor si existe
        if (isset($registro_imagen['imagen']) && $registro_imagen['imagen'] !== null) {
            $ruta_imagen = "../../../images/blogs/" . $registro_imagen['imagen'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM blogs WHERE id_blog = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA TABLA BLOGS ==============//
    $sentencia = $conexion->prepare("SELECT * FROM blogs");
    $sentencia->execute();
    $listaBlogs = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaBlogs as &$blog) {
        foreach ($blog as $key => $value) {
            $blog[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($blog); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE BLOGS -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
    <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Administrar <span class="text_red">Blogs</span>
        </span>
        <div class="d-flex gap-2">
            <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
                <i class="ri-arrow-left-s-line"></i> Página Principal
            </a>
            <a name="sitio_web" id="sitio_web" class="btn btn-danger d-flex align-items-center gap-1" href="../../../index.php" target="_blank" role="button">
                <i class="ri-earth-line"></i> Revisar Cambios
            </a>
        </div>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="table-responsive">
            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped">
                <thead>
                    <tr class="table-active">
                        <th class="w-10 text-center">ID</th>
                        <th class="w-20 text-center">TÍTULO</th>
                        <th class="w-30 text-center">RESUMEN</th>
                        <th class="w-20 text-center">IMAGEN</th>
                        <th class="w-20 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaBlogs as $blog) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $blog['id_blog']; ?></td>
                            <td><?php echo $blog['titulo_blog']; ?></td>
                            <td><?php echo $blog['resumen']; ?></td>
                            <td class="text-center">
                                <?php if ($blog['imagen']) { ?>
                                    <img src="../../../images/blogs/<?php echo $blog['imagen']; ?>" width="50" alt="Imagen Blog" style="border-radius: 5px;">
                                <?php } else { ?>
                                    <span>No disponible</span>
                                <?php } ?>
                            </td>
                            <td class="align-middle text-center">
                                <a class="btn btn-warning btn-sm mb-2 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $blog['id_blog']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $blog['id_blog']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus blogs de forma eficiente</small>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este blog?")) {
        window.location.href = "index.php?txtID=" + id;
    }
}
</script>

<!-- BOTONES FLOTANTES EN LA PANTALLA -->
<div class="main_botones_flotantes">
    <div class="botones_fixed">
        <li>
            <a class="boton_agregar" href="crear.php">
                <i class="ri-add-line"></i>
            </a>
        </li>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
