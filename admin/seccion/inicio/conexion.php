<?php

if ($_SERVER['HTTP_HOST'] === 'localhost') {

    // Configuración local XAMPP
    $servidor = "localhost";
    $bd = "constantine_bd";
    $usuario = "root";
    $contraseña = "";

} else {

    // Configuración InfinityFree
    $servidor = "sql300.infinityfree.com";
    $bd = "if0_42255430_constantine";
    $usuario = "if0_42255430";
    $contraseña = "u3X7qAHLI56Sb0";

}


try {

    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$bd;charset=utf8",
        $usuario,
        $contraseña
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );


} catch(PDOException $error){

    die("Error de conexión: ".$error->getMessage());

}


session_start();