<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la imagen
        $sentencia = $conexion->prepare("SELECT * FROM categorias WHERE id_categoria = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $registro_imagen = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar la imagen del servidor si existe
        if (isset($registro_imagen['imagen'])) {
            $ruta_imagen = "../../../images/categorias/" . $registro_imagen['imagen'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM categorias WHERE id_categoria = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA TABLA CATEGORIAS ==============//
    $sentencia = $conexion->prepare("SELECT * FROM categorias");
    $sentencia->execute();
    $listaCategorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaCategorias as &$categoria) {
        foreach ($categoria as $key => $value) {
            $categoria[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($categoria); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Administrar <span class="text_red">SubCategorías</span>
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

            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped no-datatable">
                <thead>
                    <tr>
                        <th class="w-10 text-center">ID</th>
                        <th class="w-20 text-center">NOMBRE</th>
                        <th class="w-10 text-center">IMAGEN</th>
                        <th class="w-20 text-center">UBICACIÓN</th>
                        <th class="w-20 text-center">FECHA ACTUALIZACIÓN</th>
                        <th class="w-20 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaCategorias as $categoria) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $categoria['id_categoria']; ?></td>
                            <td><?php echo $categoria['nombre_categoria']; ?></td>
                            <td class="align-middle text-center">
                                <img src="../../../images/categorias/<?php echo $categoria['imagen']; ?>" width="50" alt="imagen" style="border-radius: 5px;">
                            </td>
                            <td><?php echo $categoria['ubicacion']; ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($categoria['fecha_actualizacion'])) ; ?></td> <!-- Mostrar la fecha de actualización -->
                            <td class="align-middle text-center">
                                <a class="btn btn-warning btn-sm <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $categoria['id_categoria']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $categoria['id_categoria']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                                <a class="btn btn-success btn-sm" href="productos.php?categoriaID=<?php echo $categoria['id_categoria']; ?>" role="button">
                                    <i class="ri-t-shirt-2-fill"></i> Prendas
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
           
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus categorías de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE CATEGORÍAS PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-group-fill"></i>
            Administrar <span class="text_red">Categorías</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaCategorias as $categoria) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $categoria['id_categoria']; ?><br>
                                <strong><i class="ri-user-line"></i> Nombre:</strong> <?php echo $categoria['nombre_categoria']; ?><br>
                                <strong><i class="ri-align-left"></i> Ubicación:</strong> <?php echo $categoria['ubicacion']; ?><br>
                                <strong><i class="ri-calendar-line"></i> Actualizado:</strong> <?php echo $categoria['fecha_actualizacion']; ?><br> <!-- Nueva fila -->
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $categoria['id_categoria']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <a class="btn btn-success btn-sm mx-1" href="productos.php?categoriaID=<?php echo $categoria['id_categoria']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-t-shirt-2-fill"></i>
                                </a>
                                <button class="btn btn-danger btn-sm mx-1 <?php echo $permiso['eliminar']; ?>" onclick="confirmDelete(<?php echo $categoria['id_categoria']; ?>)" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus categorías de forma eficiente</small>
    </div>
</div>
<!-- BOTONES FLOTANTES EN LA PANTALLA -->
<div class="main_botones_flotantes">
        <div class="botones_fixed">
            <li>
                <a class="boton_agregar <?php echo $permiso['crear']; ?>" href="crear.php">
                    <i class="ri-add-line"></i>
                </a>
            </li>
        </div>
    </div>

<?php include("../../templates/footer_admin.php"); ?>

