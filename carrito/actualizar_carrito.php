<?php
session_start();
include("../admin/seccion/inicio/conexion.php");

if (isset($_POST['id_carrito'], $_POST['cantidad'])) {
    $id_carrito = intval($_POST['id_carrito']);
    $cantidad = intval($_POST['cantidad']);
    
    // Recalcular precio total
    $query = $conexion->prepare("
        UPDATE carrito 
        SET cantidad = :cantidad, 
            precio_total = cantidad * precio_unitario
        WHERE id_carrito = :id_carrito
    ");
    $query->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $query->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);

    if ($query->execute()) {
        header("Location: carrito.php");
        exit();
    } else {
        echo "Error al actualizar el carrito.";
    }
}
?>
