<?php
    include("conexion.php");

    // Inicializar variables de control de intentos
    if (!isset($_SESSION['intentos'])) {
        $_SESSION['intentos'] = 3;
    }
    if (!isset($_SESSION['bloqueado_hasta'])) {
        $_SESSION['bloqueado_hasta'] = 0;
    }

    if ($_POST) {
        $tiempo_actual = time();
        if ($_SESSION['bloqueado_hasta'] > $tiempo_actual) {
            $tiempo_restante = $_SESSION['bloqueado_hasta'] - $tiempo_actual;
            $mensaje = "Has alcanzado el límite de intentos. Espera $tiempo_restante segundos para volver a intentarlo.";
        } else {
            // Recibir el valor ingresado que puede ser un correo o un usuario
            $correo_o_usuario = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            if (!empty($correo_o_usuario) && !empty($password)) {
                try {
                    $sql = "SELECT u.*, r.rol_nombre FROM usuarios u 
                            LEFT JOIN roles r ON u.id_rol = r.id_rol 
                            WHERE u.correo = :correo_o_usuario OR u.usuario = :correo_o_usuario";
                    $sentencia = $conexion->prepare($sql);
                    $sentencia->bindParam(":correo_o_usuario", $correo_o_usuario);
                    $sentencia->execute();

                    $usuario_info = $sentencia->fetch(PDO::FETCH_ASSOC);

                    if ($usuario_info) {
                        if ($usuario_info["estado"] == 1) {
                            if (password_verify($password, $usuario_info["password"])) {
                                session_regenerate_id(true);
                                $_SESSION["id_usuario"] = $usuario_info["id_usuario"];
                                $_SESSION['id_rol'] = $usuario_info['id_rol'];
                                $_SESSION["usuario"] = $usuario_info["usuario"];
                                $_SESSION["dni"] = $usuario_info["dni"];
                                $_SESSION["nombres"] = $usuario_info["nombres"];
                                $_SESSION["apellidos"] = $usuario_info["apellidos"];
                                $_SESSION["correo"] = $usuario_info["correo"];
                                $_SESSION["direccion"] = $usuario_info["direccion"];
                                $_SESSION["telefono"] = $usuario_info["telefono"];
                                $_SESSION["rol"] = $usuario_info["rol_nombre"];
                                $_SESSION["genero"] = $usuario_info["genero"];
                                $_SESSION["foto"] = $usuario_info["foto"];
                                $_SESSION["logueado"] = true;
                                $_SESSION['intentos'] = 3;
                            
                                $sql_actualizar = "UPDATE usuarios SET fecha_ultimo_acceso = NOW() WHERE id_usuario = :id";
                                $sentencia_actualizar = $conexion->prepare($sql_actualizar);
                                $sentencia_actualizar->bindParam(":id", $usuario_info["id_usuario"]);
                                $sentencia_actualizar->execute();
                            
                                // Redirigir según el rol
                                if ($usuario_info["rol_nombre"] === "Cliente") {
                                    header("Location: http://localhost/Constantine_SitioWeb");
                                } else {
                                    header("Location: index.php");
                                }
                                exit;
                            }else {
                                $_SESSION['intentos']--;
                                if ($_SESSION['intentos'] <= 0) {
                                    $_SESSION['bloqueado_hasta'] = $tiempo_actual + 30;
                                    $mensaje = "Has alcanzado el límite de intentos. Espera 30 segundos para volver a intentarlo.";
                                } else {
                                    $mensaje = "Correo, usuario o contraseña incorrectos. Te quedan {$_SESSION['intentos']} intentos.";
                                }
                            }
                        } else {
                            $mensaje = "Tu cuenta está desactivada. Contacta con el administrador.";
                        }
                    } else {
                        $_SESSION['intentos']--;
                        if ($_SESSION['intentos'] <= 0) {
                            $_SESSION['bloqueado_hasta'] = $tiempo_actual + 10;
                            $mensaje = "Has alcanzado el límite de intentos. Espera 10 segundos para volver a intentarlo.";
                        } else {
                            $mensaje = "Correo, usuario o contraseña incorrectos. Te quedan {$_SESSION['intentos']} intentos.";
                        }
                    }
                } catch (PDOException $e) {
                    $mensaje = "Error de conexión: " . $e->getMessage();
                }
            } else {
                $mensaje = "Por favor, complete todos los campos.";
            }
        }
    }

    // Obtener permisos del usuario basado en su rol
    if (isset($_SESSION['id_rol'])) {
        $id_rol = $_SESSION['id_rol'];
        $sentencia = $conexion->prepare("
            SELECT menu_enlaces.nombre, permisos.leer
            FROM permisos
            INNER JOIN menu_enlaces ON permisos.enlace_id = menu_enlaces.id_enlace
            WHERE permisos.id_rol = :id_rol AND permisos.leer = 'visible'
        ");
        $sentencia->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $sentencia->execute();
        $permisos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        // Guardar permisos en la sesión
        $_SESSION['permisos'] = $permisos;
    }

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Constantine - Iniciar Sesión</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="icon" href="../../../images/logos/logo_white_border.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <!-- Remix Icon CSS -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
</head>
<style>
    .form-group {
        display: flex;
        gap: 15px;
    }

    .form-group .input_container {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        position: relative;
        margin: 0px;
    }

    .input_container span {
        display: inline-flex;
        align-items: center;
    }

</style>
<body>
    <div class="container" id="container">
        <!-- Formulario de Registro -->
        <div class="form-container sign-up">
        <form action="registro_usuario.php" method="post">
            <img src="../../../images/logos/logo_solo_black.png" alt="logo" width="50px">
            <div class="titulo_cuerpo">
                CREA UNA<span class="text_red"> CUENTA</span>
            </div>
            <hr>
            <?php if (isset($mensaje)): ?>
                <div class="alert alert-warning">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- Nombres y Apellidos -->
            <div class="form-group">
                <div class="input_container">
                    <span><i class="ri-user-line"></i></span>
                    <input type="text" name="nombres" placeholder="Nombres" required maxlength="255" />
                </div>
                <div class="input_container">
                    <span><i class="ri-user-line"></i></span>
                    <input type="text" name="apellidos" placeholder="Apellidos" required maxlength="255" />
                </div>
            </div>

            <!-- DNI y Teléfono -->
            <div class="form-group">
                <!-- DNI -->
                <div class="input_container">
                    <span><i class="ri-id-card-line"></i></span>
                    <input 
                        type="text" 
                        name="dni" 
                        placeholder="DNI (8 dígitos)" 
                        required 
                        maxlength="8" 
                        pattern="^\d{8}$" 
                        title="El DNI debe contener exactamente 8 dígitos numéricos." 
                    />
                </div>

                <!-- Teléfono -->
                <div class="input_container">
                    <span><i class="ri-phone-line"></i></span>
                    <input 
                        type="tel" 
                        name="telefono" 
                        placeholder="Teléfono (9 dígitos)" 
                        required 
                        maxlength="9" 
                        pattern="^9\d{8}$" 
                        title="El teléfono debe ser un número de 9 dígitos, comenzando con 9." 
                    />
                </div>
            </div>
            <!-- Dirección y Género -->
            <div class="form-group">
                <div class="input_container">
                    <span><i class="ri-map-pin-line"></i></span>
                    <input type="text" name="direccion" placeholder="Dirección" required maxlength="255" />
                </div>
                <div class="input_container">
                    <span><i class="ri-account-pin-circle-line"></i></span>
                    <select name="genero" required>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>

            <!-- Correo -->
            <div class="input_container">
                <span><i class="ri-mail-line"></i></span>
                <input type="email" name="correo" placeholder="Correo electrónico" required maxlength="50" pattern="([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,})" />
            </div>

            <!-- Contraseña -->
            <div class="input_container">
                <span><i class="ri-lock-line"></i></span>
                <input 
                    id="register_password"
                    type="password" 
                    name="password" 
                    placeholder="Contraseña" 
                    required 
                    minlength="6" 
                    maxlength="16"
                />
                <span><i class="ri-eye-line" id="togglePasswordRegister" style="cursor: pointer;"></i></span>
            </div>

            <div class="input_container">
                <span><i class="ri-lock-line"></i></span>
                <input 
                    id="register_confirm_password"
                    type="password" 
                    name="confirm_password" 
                    placeholder="Confirmar contraseña" 
                    required 
                    minlength="6" 
                    maxlength="16"
                />
                <span><i class="ri-eye-line" id="toggleConfirmPassword" style="cursor: pointer;"></i></span>
            </div>

            <button class="boton_login">
                <i class="ri-user-add-line"></i>
                <span>Registrarse</span>
            </button>
        </form>
    </div>


        <div class="form-container sign-in">

            <form action="login.php" method="post">
                <img src="../../../images/logos/logo_solo_black.png" alt="logo" width="50px">
                <div class="titulo_cuerpo">
                    INICIAR<span class="text_red"> SESIÓN</span>
                </div>
                <hr>
                <?php if (isset($mensaje)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="ri-error-warning-line"></i> <strong>Error:</strong> <?php echo $mensaje; ?>
                    </div>
                <?php } ?>
                <div class="input_container">
                    <span><i class="ri-mail-line"></i></span>
                    <input 
                        value="eupaucar@gmail.com" 
                        type="text" 
                        name="correo" 
                        id="correo" 
                        placeholder="Ingrese su correo o usuario" 
                        required 
                        pattern="([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,})|([A-Za-z0-9]+)" 
                        maxlength="50" 
                        minlength="5" 
                        title="Ingrese un correo válido (usuario@dominio.com) o un nombre de usuario (solo letras y números)." 
                    />
                </div>
                <div class="input_container">
                    <span><i class="ri-lock-line"></i></span>
                    <input 
                        value="123456" 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Ingrese su contraseña" 
                        required 
                        pattern="[A-Za-z0-9]+" 
                        maxlength="16" 
                        minlength="6" title="Solo se permiten letras y números, entre 6 y 16 caracteres."
                    />
                    <span><i class="ri-eye-line" id="togglePasswordLogin" style="cursor: pointer;"></i></span>
                </div>
                <a class="enlace" href="#">
                    <i class="ri-question-line"></i>¿Olvidaste tu contraseña?
                </a>

                <div class="botones_login">
                    <button type="submit" class="boton_login">
                        <i class="ri-login-box-line"></i> 
                        <span>Ingresar</span>
                    </button>
                    <a class="boton_login" href="../../../index.php">
                        <i class="ri-arrow-left-wide-line"></i>
                        <span>Volver</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Panel de Alternancia -->
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <div class="titulo_cuerpo">¡Bienvenido de nuevo!</div>
                    <p>Introduce tus datos personales para usar todas las funciones del sitio</p>
                    <button class="boton_login hidden" id="login">
                        <i class="ri-arrow-left-line"></i>
                        <span>Iniciar Sesión</span>
                    </button>
                </div>
                <div class="toggle-panel toggle-right">
                    <div class="titulo_cuerpo">¿No tienes una cuenta?</div>
                    <p>Regístrate para usar todas las funciones del sitio</p>
                    <button class="boton_login hidden" id="register">
                        <i class="ri-user-add-fill"></i>
                        <span>Regístrese Aquí</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('container');
        const registerBtn = document.getElementById('register');
        const loginBtn = document.getElementById('login');

        registerBtn.addEventListener('click', () => {
            container.classList.add("active");
        });

        loginBtn.addEventListener('click', () => {
            container.classList.remove("active");
        });

        // Función para alternar la visibilidad de contraseñas
function togglePasswordVisibility(toggleIconId, passwordFieldId) {
    const toggleIcon = document.querySelector(`#${toggleIconId}`);
    const passwordField = document.querySelector(`#${passwordFieldId}`);

    toggleIcon.addEventListener('click', function () {
        // Alterna entre 'password' y 'text'
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Cambia el icono de ojo
        this.classList.toggle('ri-eye-line');
        this.classList.toggle('ri-eye-off-line');
    });
}

// Llama la función para cada campo y botón
togglePasswordVisibility('togglePasswordLogin', 'login_password'); // Inicio de sesión
togglePasswordVisibility('togglePasswordRegister', 'register_password'); // Contraseña de registro
togglePasswordVisibility('toggleConfirmPassword', 'register_confirm_password'); // Confirmar contraseña
    </script>
</body>
</html>
