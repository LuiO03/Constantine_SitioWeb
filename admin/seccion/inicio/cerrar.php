<?php 
    session_start();
    session_unset();
    session_destroy();
    header("Location: login.php");
    //echo "Salir... cerrar sesión";
    exit(); // Para asegurarte de que no se ejecute más código después de la redirección
?>