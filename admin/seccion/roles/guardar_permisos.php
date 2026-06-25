<?php
include("../inicio/conexion.php");

if (isset($_POST['rol_id']) && isset($_POST['permisos'])) {
    $rol_id = $_POST['rol_id'];
    $permisos = $_POST['permisos'];

    // Eliminar permisos actuales para el rol
    $sentencia_delete = $conexion->prepare("DELETE FROM permisos WHERE id_rol = :id_rol");
    $sentencia_delete->bindParam(":id_rol", $rol_id);
    $sentencia_delete->execute();

    // Insertar nuevos permisos
    foreach ($permisos as $enlace_id) {
        $sentencia_insert = $conexion->prepare("INSERT INTO permisos (id_rol, enlace_id) VALUES (:id_rol, :enlace_id)");
        $sentencia_insert->bindParam(":id_rol", $rol_id);
        $sentencia_insert->bindParam(":enlace_id", $enlace_id);
        $sentencia_insert->execute();
    }

    // Redirigir a la página de roles después de guardar
    header("Location: index.php");
    exit();
}
?>
