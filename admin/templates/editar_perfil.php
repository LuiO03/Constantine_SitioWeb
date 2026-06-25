<?php
session_start();
require_once '../seccion/inicio/conexion.php'; // Conexión PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = $_POST['dni'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($dni) && !preg_match('/^\d{8}$/', $dni)) {
        die("DNI inválido. Debe contener 8 dígitos.");
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Correo electrónico inválido.");
    }

    if (!empty($telefono) && !preg_match('/^\d{9,20}$/', $telefono)) {
        die("Teléfono inválido.");
    }

    if (!empty($password) && $password !== $confirm_password) {
        die("Las contraseñas no coinciden.");
    }

    if (!empty($password) && strlen($password) < 8) {
        die("La contraseña debe tener al menos 8 caracteres.");
    }

    $nombre = htmlspecialchars($nombre);
    $apellidos = htmlspecialchars($apellidos);
    $correo = htmlspecialchars($correo);
    $telefono = htmlspecialchars($telefono);
    $direccion = htmlspecialchars($direccion);
    $genero = htmlspecialchars($genero);

    $password_hash = null;
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    try {
        $sql = "UPDATE usuarios SET 
                dni = :dni, 
                nombres = :nombre, 
                apellidos = :apellidos, 
                correo = :correo, 
                telefono = :telefono, 
                direccion = :direccion, 
                genero = :genero";

        if (!empty($password)) {
            $sql .= ", password = :password";
        }

        $sql .= " WHERE id_usuario = :id_usuario";

        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':genero', $genero);

        if (!empty($password)) {
            $stmt->bindParam(':password', $password_hash);
        }

        $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);

        if ($stmt->execute()) {
            $_SESSION['nombres'] = $nombre;
            $_SESSION['apellidos'] = $apellidos;
            $_SESSION['correo'] = $correo;
            $_SESSION['telefono'] = $telefono;
            $_SESSION['direccion'] = $direccion;
            $_SESSION['genero'] = $genero;
            if (isset($_SERVER['HTTP_REFERER'])) {
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
            exit;
        } else {
            die("Error al actualizar los datos.");
        }
    } catch (PDOException $e) {
        die("Error de base de datos: " . $e->getMessage());
    }
}
?>
