<?php
session_start();
include("../admin/seccion/inicio/conexion.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php"); // Redirige al login si no está autenticado
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Verificar si se ha enviado el ID del carrito para eliminar
if (isset($_POST['id_carrito'])) {
    $id_carrito = $_POST['id_carrito'];

    // Eliminar el producto del carrito
    $query = $conexion->prepare("DELETE FROM carrito WHERE id_carrito = :id_carrito AND id_usuario = :id_usuario");
    $query->bindParam(':id_carrito', $id_carrito, PDO::PARAM_INT);
    $query->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

    // Ejecutar la consulta
    if ($query->execute()) {
        // Redirigir al carrito con un mensaje de éxito
        header("Location: carrito.php");
    }
}
exit();
?>
