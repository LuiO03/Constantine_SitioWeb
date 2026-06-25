<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la imagen
        $sentencia = $conexion->prepare("SELECT * FROM locales WHERE id_local = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $registro_imagen = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar la imagen del servidor si existe
        if (isset($registro_imagen['imagen'])) {
            $ruta_imagen = "../../../images/locales/" . $registro_imagen['imagen'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM locales WHERE id_local = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA TABLA LOCALES ==============//
    $sentencia = $conexion->prepare("SELECT * FROM locales");
    $sentencia->execute();
    $listaLocales = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaLocales as &$local) {
        foreach ($local as $key => $value) {
            $local[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($local); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
    <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-price-tag-3-fill"></i>
            Administrar <span class="text_red">Locales</span>
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
                    <tr class="table-active">
                        <th class="w-10 text-center">ID</th>
                        <th class="w-20 text-center">NOMBRE</th>
                        <th class="w-20 text-center">DIRECCIÓN</th>
                        <th class="w-20 text-center">HORARIO</th>
                        <th class="w-20 text-center">TELÉFONO</th>
                        <th class="w-20 text-center">ENLACE</th>
                        <th class="w-20 text-center">IMAGEN</th>
                        <th class="w-20 text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaLocales as $local) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $local['id_local']; ?></td>
                            <td><?php echo $local['nombre_local']; ?></td>
                            <td><?php echo $local['direccion']; ?></td>
                            <td><?php echo $local['horario']; ?></td>
                            <td><?php echo $local['telefono']; ?></td>
                            <td class="text-center"><a href="<?php echo $local['enlace']; ?>" target="_blank">Ver Ubicación</a></td>
                            <td class="text-center">
                                <?php if ($local['imagen']) { ?>
                                    <img src="../../../images/locales/<?php echo $local['imagen']; ?>" width="50" alt="Imagen Local" style="border-radius: 5px;">
                                <?php } else { ?>
                                    <span>No disponible</span>
                                <?php } ?>
                            </td>
                            <td class="align-middle text-center">
                                <a class="btn btn-warning btn-sm mb-2 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $local['id_local']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $local['id_local']; ?>)">
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
        <small>Gestiona tus locales de forma eficiente</small>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm("¿Estás seguro de que deseas eliminar este local?")) {
        window.location.href = "index.php?txtID=" + id;
    }
}
</script>

<!-- BOTONES FLOTANTES EN LA PANTALLA -->
<div class="main_botones_flotantes">
    <div class="botones_fixed">
        <li>
            <a class="boton_pdf" href="generar-pdf.php">
                <i class="ri-file-pdf-fill"></i>
            </a>
        </li>
        <li>
            <a class="boton_excel" href="generar-excel.php">
                <i class="ri-file-excel-fill"></i>
            </a>
        </li>
        <li>
            <a class="boton_agregar <?php echo $permiso['crear']; ?>" href="crear.php">
                <i class="ri-add-line"></i>
            </a>
        </li>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>
