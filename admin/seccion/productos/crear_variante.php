<?php
include("../inicio/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y obtener los datos enviados desde el formulario
    $id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
    $id_color = isset($_POST['id_color']) ? (int)$_POST['id_color'] : 0;
    $id_talla = isset($_POST['id_talla']) ? (int)$_POST['id_talla'] : 0;
    $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

    // Validar campos obligatorios
    if ($id_producto > 0 && $id_color > 0 && $id_talla > 0 && $stock >= 0) {
        try {
            // Verificar si la variante ya existe (para evitar duplicados)
            $consulta = $conexion->prepare("SELECT * FROM productos_variantes 
                                            WHERE id_producto = :id_producto 
                                            AND id_color = :id_color 
                                            AND id_talla = :id_talla");
            $consulta->bindParam(':id_producto', $id_producto);
            $consulta->bindParam(':id_color', $id_color);
            $consulta->bindParam(':id_talla', $id_talla);
            $consulta->execute();
            $variante_existente = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($variante_existente) {
                // Variante ya existe
                header("Location: variantes.php?txtID=$id_producto&error=Variante ya existe");
                exit();
            } else {
                // Insertar nueva variante
                $sentencia = $conexion->prepare("INSERT INTO productos_variantes (id_producto, id_color, id_talla, stock) 
                                                 VALUES (:id_producto, :id_color, :id_talla, :stock)");
                $sentencia->bindParam(':id_producto', $id_producto);
                $sentencia->bindParam(':id_color', $id_color);
                $sentencia->bindParam(':id_talla', $id_talla);
                $sentencia->bindParam(':stock', $stock);

                if ($sentencia->execute()) {
                    // Redirigir con éxito
                    header("Location: variantes.php?txtID=$id_producto&success=Variante agregada");
                    exit();
                } else {
                    // Error al insertar
                    header("Location: variantes.php?txtID=$id_producto&error=No se pudo agregar la variante");
                    exit();
                }
            }
        } catch (Exception $e) {
            // Manejo de errores
            header("Location: variantes.php?txtID=$id_producto&error=" . $e->getMessage());
            exit();
        }
    } else {
        // Campos faltantes o inválidos
        header("Location: variantes.php?txtID=$id_producto&error=Datos inválidos");
        exit();
    }
} else {
    // Redirigir si no se accede por POST
    header("Location: index.php");
    exit();
}
?>
