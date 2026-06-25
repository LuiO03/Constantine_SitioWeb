<?php
include("../admin/seccion/inicio/conexion.php");

// Habilitar el modo de excepciones en PDO para un mejor manejo de errores

// Habilitar el modo de excepciones en PDO para un mejor manejo de errores
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception('Debe iniciar sesión para realizar un pedido.');
    }

    // Obtener el ID del usuario
    $id_usuario = $_SESSION['id_usuario'];
    echo "ID de usuario: $id_usuario <br>";  // Depuración

    // Obtener los datos del carrito del usuario
    $sql_carrito = "SELECT c.id_carrito, c.id_variante, c.cantidad, c.precio_unitario, c.precio_total, p.nombre_producto, pv.id_color, pv.id_talla
                    FROM carrito c
                    INNER JOIN productos_variantes pv ON c.id_variante = pv.id_variante
                    INNER JOIN productos p ON pv.id_producto = p.id_producto
                    WHERE c.id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql_carrito);
    $stmt->execute(['id_usuario' => $id_usuario]);
    $productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si no hay productos en el carrito, detener el proceso
    if (empty($productos_carrito)) {
        throw new Exception('El carrito está vacío.');
    }

    echo "Productos en el carrito: " . count($productos_carrito) . "<br>";  // Depuración

    // Recoger el tipo de entrega (delivery o retirar)
    if (!isset($_POST['tipo_entrega'])) {
        throw new Exception('Tipo de entrega no especificado.');
    }
    $tipo_entrega = $_POST['tipo_entrega'];
    echo "Tipo de entrega: $tipo_entrega <br>";  // Depuración

    $direccion = null;
    $id_local = null;

    // Procesar la dirección o local según el tipo de entrega
    if ($tipo_entrega == 'delivery') {
        // Si es delivery, obtener la dirección del cliente
        if (!isset($_POST['direccion']) || empty($_POST['direccion'])) {
            throw new Exception('Debe ingresar una dirección para el delivery.');
        }
        $direccion = $_POST['direccion'];
        echo "Dirección: $direccion <br>";  // Depuración
    } elseif ($tipo_entrega == 'retirar') {
        // Si es retirar, obtener el ID del local
        if (!isset($_POST['id_local']) || empty($_POST['id_local'])) {
            throw new Exception('Debe seleccionar un local para retirar el pedido.');
        }
        $id_local = $_POST['id_local'];
        echo "ID del local: $id_local <br>";  // Depuración
    } else {
        throw new Exception('Tipo de entrega no válido.');
    }

    // Registrar el pedido en la base de datos
    $sql_pedido = "INSERT INTO pedidos (id_usuario, tipo_entrega, direccion, id_local, estado)
                   VALUES (:id_usuario, :tipo_entrega, :direccion, :id_local, 'pendiente')";
    $stmt = $conexion->prepare($sql_pedido);
    $stmt->execute([
        'id_usuario' => $id_usuario,
        'tipo_entrega' => $tipo_entrega,
        'direccion' => $direccion,
        'id_local' => $id_local
    ]);

    // Obtener el ID del pedido recién creado
    $id_pedido = $conexion->lastInsertId();
    echo "ID del pedido: $id_pedido <br>";  // Depuración

    // Registrar los productos en el pedido
    foreach ($productos_carrito as $producto) {
        $sql_detalle_pedido = "INSERT INTO pedidos_productos (id_pedido, id_variante, cantidad, precio_unitario, precio_total)
                               VALUES (:id_pedido, :id_variante, :cantidad, :precio_unitario, :precio_total)";
        $stmt = $conexion->prepare($sql_detalle_pedido);
        $stmt->execute([
            'id_pedido' => $id_pedido,
            'id_variante' => $producto['id_variante'],
            'cantidad' => $producto['cantidad'],
            'precio_unitario' => $producto['precio_unitario'],
            'precio_total' => $producto['precio_total']
        ]);

        // Reducir el stock del producto
        $sql_actualizar_stock = "UPDATE productos_variantes
                                 SET stock = stock - :cantidad
                                 WHERE id_variante = :id_variante";
        $stmt = $conexion->prepare($sql_actualizar_stock);
        $stmt->execute([
            'cantidad' => $producto['cantidad'],
            'id_variante' => $producto['id_variante']
        ]);
    }

    // Vaciar el carrito después de realizar el pedido
    $sql_vaciar_carrito = "DELETE FROM carrito WHERE id_usuario = :id_usuario";
    $stmt = $conexion->prepare($sql_vaciar_carrito);
    $stmt->execute(['id_usuario' => $id_usuario]);

    // Redirigir a confirmacion.php con el ID del pedido
    header("Location: confirmacion.php?pedido_id=" . $id_pedido);
exit(); 

} catch (PDOException $e) {
    // Capturar errores de la base de datos
    echo "Error en la base de datos: " . $e->getMessage();
} catch (Exception $e) {
    // Capturar errores generales (como validaciones)
    echo "Error: " . $e->getMessage();
}
?>