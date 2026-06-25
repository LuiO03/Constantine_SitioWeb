<?php 
include("../inicio/conexion.php");

// Verificar si se envió una solicitud de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id_variante = isset($_POST['id_variante']) ? (int)$_POST['id_variante'] : 0;

    if ($id_variante > 0) {
        try {
            // Eliminar la variante
            $sentencia = $conexion->prepare("DELETE FROM productos_variantes WHERE id_variante = :id_variante");
            $sentencia->bindParam(':id_variante', $id_variante);
            $sentencia->execute();

            header("Location: variantes.php?txtID=$id_producto&success=Variante eliminada");
            exit();
        } catch (Exception $e) {
            header("Location: variantes.php?txtID=$id_producto&error=" . $e->getMessage());
            exit();
        }
    } else {
        header("Location: variantes.php?txtID=$id_producto&error=ID de variante inválido");
        exit();
    }
}

// Resto del código para obtener y mostrar variantes
if (isset($_GET['txtID']) && is_numeric($_GET['txtID'])) {
    $id_producto = (int)$_GET["txtID"];

    // Obtener datos del producto
    $sentencia = $conexion->prepare("SELECT * FROM productos WHERE id_producto = :id_producto");
    $sentencia->bindParam(":id_producto", $id_producto);
    $sentencia->execute();
    $producto = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        header("Location: index.php"); // Redirigir si el producto no existe
        exit();
    }

    // Obtener las variantes asociadas al producto
    $sentencia = $conexion->prepare("SELECT v.*, c.nombre_color, c.codigo_color, t.nombre_talla 
    FROM productos_variantes v
    LEFT JOIN colores c ON v.id_color = c.id_color
    LEFT JOIN tallas t ON v.id_talla = t.id_talla
    WHERE v.id_producto = :id_producto");
    $sentencia->bindParam(":id_producto", $id_producto);
    $sentencia->execute();
    $variantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: index.php"); // Redirigir si no hay ID válido
    exit();
}

$colores = $conexion->query("SELECT * FROM colores ORDER BY nombre_color ASC;")->fetchAll(PDO::FETCH_ASSOC);
$tallas = $conexion->query("SELECT * FROM tallas")->fetchAll(PDO::FETCH_ASSOC);

include("../../templates/header_admin.php");
?>



<div class="container-fluid">
    <!-- Encabezado -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4 rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                    <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
                        Gestionar Variantes de: 
                        <span class="text_red">
                            <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                        </span>
                    </span>
                    <div class="d-flex gap-2">
                        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
                            <i class="ri-arrow-left-s-line"></i> Página Principal
                        </a>
                        <a name="atras" id="atras" class="btn btn-success d-flex align-items-center gap-1" href="index.php" role="button">
                            <i class="ri-shirt-fill"></i> Productos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="row">
        <!-- Formulario a la izquierda -->
        <div class="col-lg-5 mb-4">
            <div class="card" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <div class="card-header">Agregar Nueva Variante</div>
                <div class="card-body">
                    <form method="POST" action="crear_variante.php">
                        <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
                        <div class="mb-3">
                            <label for="id_color" class="form-label">Color</label>
                            <div class="d-flex align-items-center">
                                <select id="id_color" name="id_color" class="form-select" required>
                                    <option value="">Seleccione un color</option>
                                    <?php foreach ($colores as $color) { ?>
                                        <option value="<?php echo $color['id_color']; ?>" data-color="<?php echo htmlspecialchars($color['codigo_color']); ?>">
                                            <?php echo htmlspecialchars($color['nombre_color']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                $('#id_color').select2({
                                    templateResult: formatColor, // Define cómo se ve cada opción
                                    templateSelection: formatColor, // Define cómo se ve la opción seleccionada
                                });

                                function formatColor(state) {
                                    if (!state.id) {
                                        return state.text; // Muestra el texto por defecto si no hay valor
                                    }

                                    const color = $(state.element).data('color'); // Obtén el color desde el atributo data-color

                                    // Crea un cuadro de color con el texto
                                    const $state = $(
                                        `<span style="display: flex; align-items: center;">
                                            <span style="width: 20px; height: 20px; background-color: ${color}; margin-right: 8px; border: 1px solid #ccc; border-radius: 2px;"></span>
                                            ${state.text}
                                        </span>`
                                    );

                                    return $state;
                                }
                            });

                        </script>

                        <style>
                            .select2-container--default .select2-selection--single {
                                height: 35px;
                            }
                            .select2-container--default .select2-selection--single .select2-selection__rendered {
                                line-height: 35px;
                            }
                            .select2-container--default .select2-selection--single .select2-selection__arrow {
                                height: 35px;
                            }
                        </style>

                        <div class="mb-3">
                            <label for="id_talla" class="form-label">Talla</label>
                            <select id="id_talla" name="id_talla" class="form-select" required>
                                <option value="">Seleccione una talla</option>
                                <?php foreach ($tallas as $talla) { ?>
                                    <option value="<?php echo $talla['id_talla']; ?>"><?php echo htmlspecialchars($talla['nombre_talla']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number"  placeholder="Ingrese Stock de la Variante" id="stock" name="stock" class="form-control" required min="0">
                        </div>
                        <button type="submit" class="btn btn-success w-100"><i class="ri-add-line"></i> Agregar Variante</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla a la derecha -->
        <div class="col-lg-7">
            <div class="card" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
                <div class="card-header">Lista de Variantes</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-light table-bordered table-hover align-middle table-striped">
                            <thead>
                                <tr class="table-active">
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nombre Color</th>
                                    <th class="text-center">Color</th>
                                    <th class="text-center">Talla</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($variantes as $variante) { ?>
                                    <tr>
                                        <td class="text-center"><?php echo $variante['id_variante']; ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($variante['nombre_color']); ?></td>
                                        <td class="text-center">
                                            <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                                <div style="width: 20px; height: 20px; border: 1px solid #ccc; background-color: <?php echo $variante['codigo_color']; ?>;"></div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo htmlspecialchars($variante['nombre_talla']); ?></td>
                                        <td class="text-center"><?php echo $variante['stock']; ?></td>
                                        <td class="text-center">
                                            <a href="editar_variante.php?txtID=<?php echo $variante['id_variante']; ?>" class="btn btn-warning btn-sm">
                                                <i class="ri-pencil-fill"></i> Editar
                                            </a>
                                            <a href="eliminar_variante.php?id_variante=<?php echo $variante['id_variante']; ?>&txtID=<?php echo $id_producto; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar esta variante?')">
                                                <i class="ri-delete-bin-2-fill"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php } ?>

        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php } ?>
        </div>
        
    </div>
</div>



<script src="../../js/script.js"></script>
        <!-- Bootstrap JavaScript Libraries -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous">
        </script>
