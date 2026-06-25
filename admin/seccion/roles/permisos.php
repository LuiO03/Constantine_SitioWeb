<?php 
include("../inicio/conexion.php");

// Obtener el ID del rol
$txtID = isset($_GET['txtID']) ? (int)$_GET['txtID'] : null;

if (!$txtID) {
    header("Location: roles.php"); // Redirige si no se especifica el ID
    exit();
}

// Consultar datos del rol
$sentencia = $conexion->prepare("SELECT * FROM roles WHERE id_rol = :id_rol");
$sentencia->bindParam(":id_rol", $txtID, PDO::PARAM_INT);
$sentencia->execute();
$rol = $sentencia->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    header("Location: roles.php"); // Redirige si el rol no existe
    exit();
}

// Consultar enlaces del menú
$sentencia_enlaces = $conexion->prepare("SELECT * FROM menu_enlaces");
$sentencia_enlaces->execute();
$enlaces = $sentencia_enlaces->fetchAll(PDO::FETCH_ASSOC);

// Consultar permisos actuales del rol
$sentencia_permisos = $conexion->prepare("SELECT * FROM permisos WHERE id_rol = :id_rol");
$sentencia_permisos->bindParam(":id_rol", $txtID, PDO::PARAM_INT);
$sentencia_permisos->execute();
$permisos = $sentencia_permisos->fetchAll(PDO::FETCH_ASSOC);

// Mapear permisos actuales para facilitar el acceso
$permisos_map = [];
foreach ($permisos as $permiso) {
    $permisos_map[$permiso['enlace_id']] = $permiso;
}

// Guardar cambios en permisos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($enlaces as $enlace) {
        $enlace_id = $enlace['id_enlace'];

        // Obtener valores de los checkboxes
        $leer = isset($_POST["leer_$enlace_id"]) ? 'visible' : 'hidden';
        $crear = isset($_POST["crear_$enlace_id"]) ? 'enabled' : 'disabled';
        $editar = isset($_POST["editar_$enlace_id"]) ? 'enabled' : 'disabled';
        $eliminar = isset($_POST["eliminar_$enlace_id"]) ? 'enabled' : 'disabled';

        if (isset($permisos_map[$enlace_id])) {
            // editar permisos existentes
            $sql = "UPDATE permisos SET leer = :leer, crear = :crear, editar = :editar, eliminar = :eliminar 
                    WHERE id_rol = :id_rol AND enlace_id = :enlace_id";
        } else {
            // Insertar nuevos permisos
            $sql = "INSERT INTO permisos (id_rol, enlace_id, leer, crear, editar, eliminar) 
                    VALUES (:id_rol, :enlace_id, :leer, :crear, :editar, :eliminar)";
        }

        $sentencia = $conexion->prepare($sql);
        $sentencia->bindParam(":id_rol", $txtID, PDO::PARAM_INT);
        $sentencia->bindParam(":enlace_id", $enlace_id, PDO::PARAM_INT);
        $sentencia->bindParam(":leer", $leer);
        $sentencia->bindParam(":crear", $crear);
        $sentencia->bindParam(":editar", $editar);
        $sentencia->bindParam(":eliminar", $eliminar);
        $sentencia->execute();
    }

    header("Location: permisos.php?txtID=$txtID");
    exit();
}

include("../../templates/header_admin.php");
?>
<style>
    /* Contenedor de checkbox personalizado */
    .custom-checkbox {
        position: relative;
        display: inline-block;
        width: 25px;
        height: 25px;
        cursor: pointer;
        margin: 0 auto;
    }

    /* Ocultar el checkbox original */
    .custom-checkbox input[type="checkbox"] {
        display: none;
    }

    /* Estilo base del checkbox */
    .custom-checkbox .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ff4d4d; /* Rojo por defecto */
        border-radius: 5px; /* Bordes redondeados */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s, transform 0.2s;
    }

    /* Cambiar a verde cuando está activado */
    .custom-checkbox input[type="checkbox"]:checked + .checkmark {
        background-color: #28a745; /* Verde */
        transform: scale(1.1); /* Ligeramente más grande */
    }

    /* Estilo del ícono cuando está activado */
    .custom-checkbox .checkmark::after {
        content: '';
        position: absolute;
        display: none;
        left: 8px;
        top: 5px;
        width: 8px;
        height: 14px;
        border: solid white;
        border-width: 0 3px 3px 0;
        transform: rotate(45deg);
    }

    /* Mostrar el ícono al estar activado */
    .custom-checkbox input[type="checkbox"]:checked + .checkmark::after {
        display: block;
    }
</style>

<div class="container mt-4">
    <div class="titulo_cuerpo">
        Gestión de Permisos para el Rol: <br>
        <strong class="text_red">
            <?php echo htmlspecialchars($rol['rol_nombre'], ENT_QUOTES, 'UTF-8'); ?>
        </strong>
    </div>
    <form method="POST">
        <div class="mb-3 text-end">
            <a name="atras" id="atras" class="btn btn-success" href="index.php" role="button">
                <i class="ri-user-star-fill"></i> Volver a Roles
            </a>
            <button type="button" class="btn btn-warning" id="toggle_select_all">
                <i class="ri-checkbox-fill"></i> Seleccionar Todo
            </button>
        </div>
        <div class="row">
            <!-- Primera columna de enlaces -->
            <div class="col-md-6">
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                        <tr>
                            <th>Enlace</th>
                            <th class="text-center">Leer</th>
                            <th class="text-center">Crear</th>
                            <th class="text-center">Editar</th>
                            <th class="text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $midpoint = ceil(count($enlaces) / 2); 
                        for ($i = 0; $i < $midpoint; $i++) {
                            $enlace = $enlaces[$i];
                            $permiso_actual = $permisos_map[$enlace['id_enlace']] ?? ['leer' => 'hidden', 'crear' => 'disabled', 'editar' => 'disabled', 'eliminar' => 'disabled'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($enlace['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="leer_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['leer'] === 'visible' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="crear_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['crear'] === 'enabled' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="editar_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['editar'] === 'enabled' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="eliminar_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['eliminar'] === 'enabled' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Segunda columna de enlaces -->
            <div class="col-md-6">
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                        <tr>
                            <th>Enlace</th>
                            <th class="text-center">Leer</th>
                            <th class="text-center">Crear</th>
                            <th class="text-center">Editar</th>
                            <th class="text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        for ($i = $midpoint; $i < count($enlaces); $i++) {
                            $enlace = $enlaces[$i];
                            $permiso_actual = $permisos_map[$enlace['id_enlace']] ?? ['leer' => 'hidden', 'crear' => 'disabled', 'editar' => 'disabled', 'eliminar' => 'disabled'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($enlace['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="leer_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['leer'] === 'visible' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="crear_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['crear'] === 'enabled' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="editar_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['editar'] === 'enabled' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <label class="custom-checkbox">
                                    <input type="checkbox" name="eliminar_<?php echo $enlace['id_enlace']; ?>" <?php echo $permiso_actual['eliminar'] === 'enabled' ? 'checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3 text-center">
            <button type="submit" class="btn btn-success">
                <i class="ri-save-3-fill"></i> Guardar Permisos
            </button>
            <a href="index.php" class="btn btn-danger ">
                <i class="ri-close-line"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    // Alternar entre seleccionar todo y deseleccionar todo
    document.getElementById('toggle_select_all').addEventListener('click', function () {
        // Obtener el texto actual del botón
        var button = this;
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        
        if (button.textContent.trim() === "Seleccionar Todo") {
            // Seleccionar todos los checkboxes
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = true;
            });
            // Cambiar texto del botón
            button.innerHTML = '<i class="ri-checkbox-line"></i> Deseleccionar Todo';
        } else {
            // Deseleccionar todos los checkboxes
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            // Cambiar texto del botón
            button.innerHTML = '<i class="ri-checkbox-fill"></i> Seleccionar Todo';
        }
    });
</script>




<?php include("../../templates/footer_admin.php"); ?>
