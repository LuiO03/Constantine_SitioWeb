<?php 
    include("../inicio/conexion.php");

    //============== GET CONSULTA ID PARA BORRARLO ==============//
    if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
        $txtID = (int)$_GET["txtID"];

        // Consultar el registro para obtener la información del rol
        $sentencia = $conexion->prepare("SELECT * FROM roles WHERE id_rol = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        $registro_rol = $sentencia->fetch(PDO::FETCH_LAZY);

        // Eliminar el registro de la base de datos
        $sql = "DELETE FROM roles WHERE id_rol = :id";
        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        header("Location:index.php");
        exit(); // Detener el script después de la redirección
    }

    //============== CONSULTA TABLA ROL ==============//
    $sentencia = $conexion->prepare("SELECT * FROM roles");
    $sentencia->execute();
    $listaRol = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaRol as &$rol) {
        foreach ($rol as $key => $value) {
            $rol[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($rol); // Evitar referencias no deseadas

    //============== CONSULTA PERMISOS DEL ROL ACTUAL ==============//
    $id_rol_actual = $_SESSION['id_rol'] ?? null; // Rol del usuario desde la sesión
    $enlaces_permitidos = [];

    if ($id_rol_actual) {
        $sentencia_permisos = $conexion->prepare("SELECT enlace_id FROM permisos WHERE id_rol = :id_rol");
        $sentencia_permisos->bindParam(":id_rol", $id_rol_actual);
        $sentencia_permisos->execute();
        $enlaces_permitidos = $sentencia_permisos->fetchAll(PDO::FETCH_COLUMN);
    }

    // Escapar enlaces por seguridad
    $enlaces_permitidos = array_map('htmlspecialchars', $enlaces_permitidos);

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-star-fill"></i>
            Administrar <span class="text_red">Roles</span>
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
                        <th class="w-20 text-center">Rol</th>
                        <th class="w-30 text-center">Descripción</th>
                        <th class="w-10 text-center">Estado</th>
                        <th class="w-30 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaRol as $rol) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $rol['id_rol']; ?></td>
                            <td><?php echo $rol['rol_nombre']; ?></td>
                            <td><?php echo $rol['descripcion']; ?></td>
                            <td class="text-center">
                                <?php if ($rol['estado'] == 1) { ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php } ?>
                            </td>
                            <td class="align-middle text-center">
                                <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $rol['id_rol']; ?>" role="button">
                                    <i class="ri-pencil-fill"></i> Editar
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $rol['id_rol']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                                <a class="btn btn-secondary btn-sm mx-1" href="permisos.php?txtID=<?php echo $rol['id_rol']; ?>" role="button">
                                    <i class="ri-key-fill"></i> Permisos
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus roles de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE ROLES PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-fill"></i>
            Administrar <span class="text_red">Roles</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaRol as $rol) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $rol['id_rol']; ?><br>
                                <strong><i class="ri-user-line"></i> Nombre:</strong> <?php echo $rol['rol_nombre']; ?><br>
                                <strong><i class="ri-align-left"></i> Descripción:</strong> <?php echo $rol['descripcion']; ?><br>
                                <strong><i class="ri-checkbox-circle-line"></i> Estado:</strong>
                                <?php if ($rol['estado'] == 1) { ?>
                                    <span class="badge bg-success">Activo</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger">Inactivo</span>
                                <?php } ?>
                                <br>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <a class="btn btn-warning btn-sm mx-1 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $rol['id_rol']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-pencil-fill"></i>
                                </a>
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $rol['id_rol']; ?>)" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                                <a class="btn btn-secondary btn-sm mx-1" href="permisos.php?txtID=<?php echo $rol['id_rol']; ?>" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-key-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus roles de forma eficiente</small>
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
