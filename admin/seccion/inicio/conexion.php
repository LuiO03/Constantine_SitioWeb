<?php
    $servidor = "sql300.infinityfree.com";
    $bd = "if0_42255430_constantine";
    $usuario = "if0_42255430";
    $contraseña = "u3X7qAHLI56Sb0";

    try {
        $conexion = new PDO("mysql:host=$servidor;dbname=$bd", $usuario, $contraseña);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo  "Conección establecida correctamente...";
    } catch (PDOException $error) {
        //echo  "Error al conectar a la base de datos: ".$error->getMessage();
    }
    session_start();
?>
