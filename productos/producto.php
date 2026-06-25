<?php
include("../admin/seccion/inicio/conexion.php");

if (isset($_GET['id_producto'])) {
    $id_producto = intval($_GET['id_producto']);

    // Consulta para obtener el producto base
    $sentencia_producto = $conexion->prepare("SELECT productos.*, publico.nombre_publico 
        FROM productos
        LEFT JOIN publico ON productos.id_publico = publico.id_publico
        WHERE productos.id_producto = :id_producto");
    $sentencia_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sentencia_producto->execute();
    $producto = $sentencia_producto->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        die("Producto no encontrado.");
    }

    // Consulta para las variantes del producto
    $sentencia_variantes = $conexion->prepare("SELECT pv.id_variante, c.id_color, c.nombre_color, c.codigo_color, 
        t.nombre_talla, pv.id_talla, pv.stock
        FROM productos_variantes pv
        INNER JOIN colores c ON pv.id_color = c.id_color
        INNER JOIN tallas t ON pv.id_talla = t.id_talla
        WHERE pv.id_producto = :id_producto
        ORDER BY FIELD(t.nombre_talla, 'S', 'M', 'L', 'XL', 'XXL')");
    $sentencia_variantes->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $sentencia_variantes->execute();
    $variantes = $sentencia_variantes->fetchAll(PDO::FETCH_ASSOC);
    
    // Manejo del formulario POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_variante'], $_POST['cantidad'], $_POST['precio_unitario'])) {
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        if (!$id_usuario) {
            die("Debes iniciar sesión para agregar productos al carrito.");
        }

        $id_variante = intval($_POST['id_variante']);
        $cantidad = intval($_POST['cantidad']);
        $precio_unitario = floatval($_POST['precio_unitario']);
        $precio_total = $cantidad * $precio_unitario;

        // Verificar si ya existe el mismo producto (id_variante) en el carrito
        $query_verificar = $conexion->prepare("SELECT cantidad FROM carrito 
                                               WHERE id_usuario = :id_usuario 
                                               AND id_variante = :id_variante");
        $query_verificar->bindParam(':id_usuario', $id_usuario);
        $query_verificar->bindParam(':id_variante', $id_variante);
        $query_verificar->execute();
        $registro_existente = $query_verificar->fetch(PDO::FETCH_ASSOC);

        if ($registro_existente) {
            // Si el producto ya existe en el carrito, actualizar cantidad y precio_total
            $nueva_cantidad = $registro_existente['cantidad'] + $cantidad;
            $nuevo_precio_total = $nueva_cantidad * $precio_unitario;

            $query_actualizar = $conexion->prepare("UPDATE carrito 
                                                    SET cantidad = :nueva_cantidad, 
                                                        precio_total = :nuevo_precio_total 
                                                    WHERE id_usuario = :id_usuario 
                                                    AND id_variante = :id_variante");
            $query_actualizar->bindParam(':nueva_cantidad', $nueva_cantidad);
            $query_actualizar->bindParam(':nuevo_precio_total', $nuevo_precio_total);
            $query_actualizar->bindParam(':id_usuario', $id_usuario);
            $query_actualizar->bindParam(':id_variante', $id_variante);

            if ($query_actualizar->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?id_producto=" . $id_producto);
                exit();
            } else {
                echo "Error al actualizar el carrito.";
            }
        } else {
            // Si no existe, insertar un nuevo registro
            $query_insertar = $conexion->prepare("INSERT INTO carrito (id_usuario, id_variante, cantidad, precio_unitario, precio_total) 
                                                  VALUES (:id_usuario, :id_variante, :cantidad, :precio_unitario, :precio_total)");
            $query_insertar->bindParam(':id_usuario', $id_usuario);
            $query_insertar->bindParam(':id_variante', $id_variante);
            $query_insertar->bindParam(':cantidad', $cantidad);
            $query_insertar->bindParam(':precio_unitario', $precio_unitario);
            $query_insertar->bindParam(':precio_total', $precio_total);

            if ($query_insertar->execute()) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?id_producto=" . $id_producto);
                exit();
            } else {
                echo "Error al agregar al carrito.";
            }
        }
    }
} else {
    die("ID del producto no proporcionado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constantine - Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/nav.css">
    <link rel="stylesheet" href="../style/base.css">
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="producto.css">
    <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
</head>
<body>
    <?php include("../layout/header.php"); ?>

    <main>
        <div class="contenedor_producto bloque_1">
            
            <div class="columna-principal sub_bloque">
    
                <div class="carrusel-contenido">
    
                    <div class="carrusel_producto" id="carrusel_producto">

                        <div class="imagen imagen-activa">
                            <img src="../images/productos/<?php echo strtolower($producto['nombre_publico']); ?>/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre_producto']; ?>">
                        </div>
                        
                        <div class="imagen">
                            <img src="../images/productos/caballeros/polos/item_2.png" alt="camiseta">
                        </div>
                        <div class="imagen">
                            <img src="../images/productos/caballeros/polos/item_3.png" alt="camiseta">
                        </div>
                        <div class="boton-izquierda" onclick="anterior()">
                            <i class="ri-arrow-left-s-fill ri-2x"></i>
                        </div>
                        <div class="boton-derecha" onclick="siguiente()">
                            <i class="ri-arrow-right-s-fill ri-2x"></i>
                        </div>
                    </div>
                </div>
    
                <div class="descripcion-producto">
                    <div class="titulo_cuerpo"><?php echo htmlspecialchars($producto['nombre_producto']); ?></div>
                    <p class="descripcion"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <div class="precio-producto">
                        <sup>S/.</sup><?php echo number_format($producto['precio_venta'], 2); ?>
                    </div>
                    <p class="codigo-producto">Código del producto: <?php echo htmlspecialchars($producto['codigo_producto']); ?></p>

                    <form method="POST" action="">
    <input type="hidden" name="id_variante" id="id_variante" value="">
    <input type="hidden" name="precio_unitario" id="precio_unitario" value="">

    <div class="fila-formulario">
        <!-- Selector de colores -->
        <div class="campo-formulario">
            <label for="color">
                
                <div id="mensaje-seleccion" class="mensaje">
                    Seleccione un Color
                </div>
            </label>
            <div class="opciones-colores">
                <?php 
                    $colores_mostrados = [];
                    foreach ($variantes as $variante) {
                        if (!in_array($variante['nombre_color'], $colores_mostrados)) {
                            echo "<div class='cuadro-color' 
                                        data-color-id='{$variante['id_color']}' 
                                        data-precio='{$producto['precio_venta']}' 
                                        style='background-color: {$variante['codigo_color']}'>
                                </div>";
                            $colores_mostrados[] = $variante['nombre_color'];
                        }
                    }
                ?>
            </div>
        </div>
        

        <!-- Selector de tallas -->
        <div class="campo-formulario">
            <label for="tamanio">
                
                <div id="mensaje-seleccion" class="mensaje">
                    Seleccione una Talla
                </div>
            </label>
            <div class="opciones-tallas">
                <?php 
                    foreach ($variantes as $variante) {
                        echo "<div class='cuadro-talla inactivo' 
                                data-color-id='{$variante['id_color']}' 
                                data-id-variante='{$variante['id_variante']}'>
                            {$variante['nombre_talla']}
                        </div>";
                    }
                ?>
            </div>
        </div>

        <!-- Control de cantidad -->
        <div class="campo-formulario cantidad-control">
            <label for="cantidad">Cantidad</label>
            <div class="input-cantidad">
                <button type="button" id="btn-menos">
                    <i class="ri-subtract-line"></i>
                </button>
                <input type="number" name="cantidad" id="cantidad" min="1" value="1">
                <button type="button" id="btn-mas">
                    <i class="ri-add-line"></i>
                </button>
            </div>
        </div>

        <!-- Botón de agregar -->
        <div class="boton_contenedor">
            <button type="submit" class="boton_general">
                <i class="ri-shopping-cart-line"></i>
                <span>AGREGAR AL CARRITO</span>
            </button>
        </div>
    </div>
</form>

                </div>
            </div>
        </div>
        
        <div class="contenedor_detalle bloque_1">
    
            <div class="pestanas-producto sub_bloque">
                <ul class="lista-pestanas" id="pestanas">
                    <li class="pestana-activa titulo_lista">Características</li>
                    <li class="titulo_lista">Tamaños</li>
                </ul>
                <div class="contenido-pestanas">
    
                    <div class="panel-descripcion active sub_bloque">
                        <table class="tabla-descripcion">
                            <tr>
                                <th>Marca</th>
                                <td>SWISS LORD</td>
                            </tr>
                            <tr>
                                <th>Peso (kg)</th>
                                <td>-</td>
                            </tr>
                            <tr>
                                <th>Tipo de producto</th>
                                <td>Polos</td>
                            </tr>
                            <tr>
                                <th>Género</th>
                                <td>Hombre</td>
                            </tr>
                            <tr>
                                <th>Modelo</th>
                                <td>Raglan</td>
                            </tr>
                            <tr>
                                <th>Material</th>
                                <td>Algodón</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="panel-tamanios sub_bloque">
                        <table class="tabla-tamanios">
                            <thead>
                                <tr>
                                    <th>Talla</th>
                                    <th>Pecho (cm)</th>
                                    <th>Cintura (cm)</th>
                                    <th>Largo (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>S</td>
                                    <td>90-95</td>
                                    <td>80-85</td>
                                    <td>65</td>
                                </tr>
                                <tr>
                                    <td>M</td>
                                    <td>95-100</td>
                                    <td>85-90</td>
                                    <td>67</td>
                                </tr>
                                <tr>
                                    <td>L</td>
                                    <td>100-105</td>
                                    <td>90-95</td>
                                    <td>69</td>
                                </tr>
                                <tr>
                                    <td>XL</td>
                                    <td>105-110</td>
                                    <td>95-100</td>
                                    <td>71</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>

        // Botones cantidad
const btnMas = document.getElementById('btn-mas');
    const btnMenos = document.getElementById('btn-menos');
    const inputCantidad = document.getElementById('cantidad');

    btnMas.addEventListener('click', () => {
        inputCantidad.value = parseInt(inputCantidad.value || 1) + 1;
    });

    btnMenos.addEventListener('click', () => {
        if (inputCantidad.value > 1) {
            inputCantidad.value = parseInt(inputCantidad.value || 1) - 1;
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
    const colorBoxes = document.querySelectorAll('.cuadro-color');
    const tallaBoxes = document.querySelectorAll('.cuadro-talla');
    const inputVariante = document.getElementById('id_variante');
    const inputPrecio = document.getElementById('precio_unitario');
    const form = document.querySelector('form');

    // Deshabilitar todas las tallas inicialmente
    tallaBoxes.forEach(tallaBox => {
        tallaBox.classList.add('inactivo');
    });

    // Manejo de selección de colores
    colorBoxes.forEach(colorBox => {
        colorBox.addEventListener('click', () => {
            // Quitar selección previa y marcar el color seleccionado
            colorBoxes.forEach(cb => cb.classList.remove('seleccionado'));
            colorBox.classList.add('seleccionado');

            const selectedColorId = colorBox.getAttribute('data-color-id');

            // Habilitar las tallas asociadas al color seleccionado
            tallaBoxes.forEach(tallaBox => {
                if (tallaBox.getAttribute('data-color-id') === selectedColorId) {
                    tallaBox.classList.remove('inactivo');
                } else {
                    tallaBox.classList.add('inactivo');
                }
            });

            // Establecer precio unitario (opcional)
            inputPrecio.value = colorBox.getAttribute('data-precio');
        });
    });

    // Manejo de selección de tallas
    tallaBoxes.forEach(tallaBox => {
        tallaBox.addEventListener('click', () => {
            if (!tallaBox.classList.contains('inactivo')) {
                // Quitar selección previa y marcar la talla seleccionada
                tallaBoxes.forEach(tb => tb.classList.remove('seleccionado'));
                tallaBox.classList.add('seleccionado');

                // Establecer id_variante
                inputVariante.value = tallaBox.getAttribute('data-id-variante');
            }
        });
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', (event) => {
        if (!inputVariante.value || !inputPrecio.value) {
            event.preventDefault(); // Detener el envío del formulario
            alert('Por favor, selecciona un color y una talla antes de agregar al carrito.');
        }
    });
});



</script>s
    <!--=============== FOOTER ===============-->
   <?php include("../layout/footer.php"); ?>
