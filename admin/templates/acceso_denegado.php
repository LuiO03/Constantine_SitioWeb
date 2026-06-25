<?php
// Inicia la sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/header.css">
    <!-- Remix Icon -->
    <link href="../../libs/remix/fonts/remixicon.css" rel="stylesheet">
    <link rel="icon" href="../../images/logos/logo_white_border.png" type="image/png">
    <style>
        body {
            background: var(--color_hover_modulo);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .error-icon {
            font-size: 4rem;
            color: #d9534f;
            margin-bottom: 20px;
        }
        h1 {
            font-weight: bold;
            margin-bottom: 15px;
        }
        p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .btn-home {
            background-color: #d9534f;
            color: #fff;
        }
        .btn-home:hover {
            background-color: #c9302c;
        }
        .btn-account {
            background-color: #0275d8;
            color: #fff;
        }
        .btn-account:hover {
            background-color: #025aa5;
        }
        .image-container img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-container">
            <img src="../../images/logos/logo_solo_black.png" alt="Error Icon" width="100px">
        </div>
        <i class="ri-shield-cross-line error-icon"></i>
        <h1 class="titulo_cuerpo">ACCESO DENEGADO</h1>
        <p>
            No tienes los permisos necesarios para acceder a esta página. <br>
            Por favor, verifica tus credenciales o contacta al administrador.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="../seccion/inicio/index.php" class="btn btn-home">
                <i class="ri-home-line"></i> Volver al Inicio
            </a>
            <a href="../seccion/inicio/cerrar.php" class="btn btn-account">
                <i class="ri-logout-box-line"></i> Cambiar de Cuenta
            </a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

