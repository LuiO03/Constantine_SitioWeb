<?php
include("../admin/seccion/inicio/conexion.php");

// Obtener el ID del pedido desde la URL
if (!isset($_GET['pedido_id']) || empty($_GET['pedido_id'])) {
    echo "Error: ID de pedido no válido.";
    exit;
}

$pedido_id = $_GET['pedido_id'];

try {
    // Consultar los detalles del pedido
    $stmt = $conexion->prepare("SELECT p.*, pd.*, u.nombres AS nombre_usuario, u.correo
                                FROM pedidos p
                                JOIN pedidos_productos pd ON p.id_pedido = pd.id_pedido
                                JOIN usuarios u ON p.id_usuario = u.id_usuario
                                WHERE p.id_pedido = :pedido_id");
    $stmt->bindParam(':pedido_id', $pedido_id);
    $stmt->execute();
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el pedido existe
    if (!$pedido) {
        echo "Error: Pedido no encontrado.";
        exit;
    }

    // Mostrar detalles del pedido
    echo "<div class='pedido-container'>";
    echo "<h1>Pedido Confirmado</h1>";
    echo "<p><strong>ID del pedido:</strong> " . $pedido['id_pedido'] . "</p>";
    echo "<p><strong>Nombre del usuario:</strong> " . $pedido['nombre_usuario'] . "</p>";
    echo "<p><strong>Email:</strong> " . $pedido['correo'] . "</p>"; // Corregido a 'correo'
    echo "<p><strong>Tipo de entrega:</strong> " . $pedido['tipo_entrega'] . "</p>";

    // Mostrar dirección o local de entrega
    if ($pedido['tipo_entrega'] == 'delivery') {
        echo "<p><strong>Dirección de entrega:</strong> " . $pedido['direccion'] . "</p>";
    } else {
        // Consultar el nombre del local de retiro
        $stmt_local = $conexion->prepare("SELECT nombre_local FROM locales WHERE id_local = :id_local");
        $stmt_local->bindParam(':id_local', $pedido['id_local']);
        $stmt_local->execute();
        $local = $stmt_local->fetch(PDO::FETCH_ASSOC);
        
        if ($local) {
            echo "<p><strong>Local de retiro:</strong> " . $local['nombre_local'] . "</p>";
        } else {
            echo "<p>Local no encontrado.</p>";
        }
    }

    // Mostrar los detalles de los productos del pedido
    echo "<h3>Detalles de los productos:</h3>";
    $stmt_detalles = $conexion->prepare("SELECT p.nombre_producto, pv.id_color, pv.id_talla, pd.cantidad, pd.precio_unitario, pd.precio_total
                                        FROM pedidos_productos pd
                                        JOIN productos_variantes pv ON pd.id_variante = pv.id_variante
                                        JOIN productos p ON pv.id_producto = p.id_producto
                                        WHERE pd.id_pedido = :pedido_id");
    $stmt_detalles->bindParam(':pedido_id', $pedido_id);
    $stmt_detalles->execute();
    $productos = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        echo "<table class='productos-table'>";
        echo "<thead><tr><th>Producto</th><th>Color</th><th>Talla</th><th>Cantidad</th><th>Precio Unitario</th><th>Precio Total</th></tr></thead>";
        echo "<tbody>";
        foreach ($productos as $producto) {
            // Consultar los nombres de color y talla
            $stmt_color = $conexion->prepare("SELECT nombre_color FROM colores WHERE id_color = :id_color");
            $stmt_color->bindParam(':id_color', $producto['id_color']);
            $stmt_color->execute();
            $color = $stmt_color->fetch(PDO::FETCH_ASSOC);

            $stmt_talla = $conexion->prepare("SELECT nombre_talla FROM tallas WHERE id_talla = :id_talla");
            $stmt_talla->bindParam(':id_talla', $producto['id_talla']);
            $stmt_talla->execute();
            $talla = $stmt_talla->fetch(PDO::FETCH_ASSOC);

            echo "<tr>";
            echo "<td>" . $producto['nombre_producto'] . "</td>";
            echo "<td>" . ($color ? $color['nombre_color'] : 'Color no encontrado') . "</td>";
            echo "<td>" . ($talla ? $talla['nombre_talla'] : 'Talla no encontrada') . "</td>";
            echo "<td>" . $producto['cantidad'] . "</td>";
            echo "<td>" . $producto['precio_unitario'] . "</td>";
            echo "<td>" . $producto['precio_total'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p>No se encontraron productos asociados a este pedido.</p>";
    }

    echo "<div class='botones-container'>";
    echo "<a href='../index.php' class='btn ir-tienda'>Ir a la tienda</a>";
    echo "<a href='javascript:history.back()' class='btn atras'>Atrás</a>";
    echo "</div>";

    echo "</div>";

} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .pedido-container {
        background-color: white;
        padding: 20px;
        margin: 30px auto;
        max-width: 900px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
    }

    p {
        font-size: 16px;
        color: #555;
        margin: 10px 0;
    }

    .productos-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .productos-table th, .productos-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .productos-table th {
        background-color: #f2f2f2;
    }

    .botones-container {
        text-align: center;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 20px;
        margin: 5px;
        text-decoration: none;
        color: white;
        border-radius: 5px;
        font-weight: bold;
        display: inline-block;
    }

    .ir-tienda {
        background-color: #28a745;
    }

    .atras {
        background-color: #007bff;
    }

    .btn:hover {
        opacity: 0.8;
    }
</style>
