<?php
session_start();
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización y validación de datos
    $dni = filter_input(INPUT_POST, 'dni', FILTER_SANITIZE_STRING);
    $nombres = filter_input(INPUT_POST, 'nombres', FILTER_SANITIZE_STRING);
    $apellidos = filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
    $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
    $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
    $genero = filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    // Validación adicional
    if (empty($dni) || !preg_match('/^\d{8}$/', $dni)) {
        $mensaje = "El DNI debe contener exactamente 8 dígitos.";
    } elseif (empty($telefono) || !preg_match('/^9\d{8}$/', $telefono)) {
        $mensaje = "El teléfono debe ser un número de 9 dígitos que comience con 9.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "El correo ingresado no es válido.";
    } elseif ($password !== $confirm_password) {
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6 || strlen($password) > 16) {
        $mensaje = "La contraseña debe tener entre 6 y 16 caracteres.";
    } else {
        try {
            // Comprobar si el correo ya existe
            $sql = "SELECT correo FROM usuarios WHERE correo = :correo";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $mensaje = "El correo ya está registrado.";
            } else {
                // Hash de la contraseña
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);

                // Insertar usuario
                $sql = "INSERT INTO usuarios 
                        (dni, nombres, apellidos, correo, telefono, direccion, genero, password, id_rol, estado) 
                        VALUES 
                        (:dni, :nombres, :apellidos, :correo, :telefono, :direccion, :genero, :password, 5, 1)";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':dni', $dni);
                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':genero', $genero);
                $stmt->bindParam(':password', $password_hashed);

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Registro exitoso. ¡Bienvenido a Constantine!";
                    header("Location: login.php");
                    exit;
                } else {
                    $mensaje = "Hubo un error al registrar al usuario. Inténtalo nuevamente.";
                }
            }
        } catch (PDOException $e) {
            $mensaje = "Error en la conexión: " . $e->getMessage();
        }
    }
}
?>
