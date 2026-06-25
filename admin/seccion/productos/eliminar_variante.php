<?php
include("../inicio/conexion.php");

// Verificar si se envió una solicitud de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_variante']) && is_numeric($_GET['id_variante'])) {
    $id_variante = (int)$_GET['id_variante'];

    if ($id_variante > 0) {
        try {
            // Eliminar la variante
            $sentencia = $conexion->prepare("DELETE FROM productos_variantes WHERE id_variante = :id_variante");
            $sentencia->bindParam(':id_variante', $id_variante);
            $sentencia->execute();

            // Redirigir con mensaje de éxito
            header("Location: variantes.php?txtID=" . $_GET['txtID'] . "&success=Variante eliminada");
            exit();
        } catch (Exception $e) {
            // Redirigir con mensaje de error
            header("Location: variantes.php?txtID=" . $_GET['txtID'] . "&error=" . $e->getMessage());
            exit();
        }
    } else {
        header("Location: variantes.php?txtID=" . $_GET['txtID'] . "&error=ID de variante inválido");
        exit();
    }
} else {
    header("Location: variantes.php?txtID=" . $_GET['txtID'] . "&error=ID de variante no proporcionado");
    exit();
}
?>
