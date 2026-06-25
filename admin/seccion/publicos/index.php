<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la información del público
        $sentencia = $conexion->prepare("SELECT * FROM publico WHERE id_publico = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $registro_publico = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM publico WHERE id_publico = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA TABLA PUBLICO ==============//
    $sentencia = $conexion->prepare("SELECT * FROM publico");
    $sentencia->execute();
    $listaPublico = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaPublico as &$publico) {
        foreach ($publico as $key => $value) {
            $publico[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($publico); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-group-2-fill"></i>
            Administrar <span class="text_red">Categorías</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i> Página Principal
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="table-responsive">
            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped no-datatable">
                <thead>
                    <tr>
                        <th class="w-10 text-center">ID</th>
                        <th class="w-30 text-center">Nombre</th>
                        <th class="w-40 text-center">Descripción</th>
                        <th class="w-20 text-center">Fecha de Creación</th>
                        <th class="w-20 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaPublico as $publico) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $publico['id_publico']; ?></td>
                            <td><?php echo $publico['nombre_publico']; ?></td>
                            <td><?php echo $publico['descripcion']; ?></td>
                            <td class="text-center"><?php echo date("d/m/Y H:i", strtotime($publico['fecha_creacion'])); ?></td>
                            <td class="align-middle text-center">
                                <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $publico['id_publico']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $publico['id_publico']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                                <a class="btn btn-success btn-sm mx-1" href="productos.php?txtID=<?php echo $publico['id_publico']; ?>" role="button">
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
        <small>Gestiona tus Categorías de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE PUBLICO PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-group-fill"></i>
            Administrar <span class="text_red">Público</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaPublico as $publico) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $publico['id_publico']; ?><br>
                                <strong><i class="ri-user-line"></i> Nombre:</strong> <?php echo $publico['nombre_publico']; ?><br>
                                <strong><i class="ri-align-left"></i> Descripción:</strong> <?php echo $publico['descripcion']; ?><br>
                                <strong><i class="ri-calendar-line"></i> Fecha de Creación:</strong> <?php echo date("d/m/Y H:i", strtotime($publico['fecha_creacion'])); ?><br>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2 <?php echo $permiso['editar']; ?>">
                                <a class="btn btn-warning btn-sm mx-1" href="editar.php?txtID=<?php echo $publico['id_publico']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <a class="btn btn-success btn-sm mx-1" href="productos.php?txtID=<?php echo $publico['id_publico']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-t-shirt-2-fill"></i>
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $publico['id_publico']; ?>)" style="min-width: 40px; min-height: 40px;">
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
        <small>Gestiona tus Categorías de forma eficiente</small>
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
