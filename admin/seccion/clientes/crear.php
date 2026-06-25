<?php 
    include("../inicio/conexion.php");

    if ($_POST) {
        // Captura y sanitización de los datos del formulario
        $dni = htmlspecialchars(trim(isset($_POST["dni"]) ? $_POST["dni"] : ""));
        $nombres = htmlspecialchars(trim(isset($_POST["nombres"]) ? $_POST["nombres"] : ""));
        $apellidos = htmlspecialchars(trim(isset($_POST["apellidos"]) ? $_POST["apellidos"] : ""));
        $correo = htmlspecialchars(trim(isset($_POST["correo"]) ? $_POST["correo"] : ""));
        $telefono = htmlspecialchars(trim(isset($_POST["telefono"]) ? $_POST["telefono"] : ""));
        $direccion = htmlspecialchars(trim(isset($_POST["direccion"]) ? $_POST["direccion"] : ""));
        $genero = htmlspecialchars(trim(isset($_POST["genero"]) ? $_POST["genero"] : ""));
        $password = htmlspecialchars(trim(isset($_POST["password"]) ? $_POST["password"] : ""));
        $confirm_password = htmlspecialchars(trim(isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : ""));
        $foto = isset($_FILES["foto"]["name"]) ? $_FILES["foto"]["name"] : "";
        $pesoImagen = isset($_FILES["foto"]["size"]) ? $_FILES["foto"]["size"] : 0;

        try {
            // Validaciones del servidor
            if (!preg_match("/^\d{8}$/", $dni)) {
                $mensajeError = "El DNI debe contener exactamente 8 dígitos.";
            } else {
                $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE dni = :dni");
                $stmt->bindParam(":dni", $dni);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    $mensajeError = "El DNI ya está registrado.";
                }
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $mensajeError = "El correo ingresado no es válido.";
            } else {
                $stmt = $conexion->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = :correo");
                $stmt->bindParam(":correo", $correo);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    $mensajeError = "El correo ya está registrado.";
                }
            }

            if (!preg_match("/^\d{9}$/", $telefono)) {
                $mensajeError = "El teléfono debe contener exactamente 9 dígitos.";
            }

            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{6,}$/", $password)) {
                $mensajeError = "La contraseña debe tener al menos 6 caracteres, incluyendo una letra y un número.";
            }

            if ($password !== $confirm_password) {
                $mensajeError = "Las contraseñas no coinciden.";
            }

            $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
            $fileExtension = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
            if ($foto != "" && !in_array($fileExtension, $allowedExtensions)) {
                $mensajeError = "Solo se permiten imágenes con extensiones jpg, jpeg, png o gif.";
            } elseif ($foto != "" && $pesoImagen > 3145728) {
                $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
            }

            if (!isset($mensajeError)) {
                // Subir imagen
                $nombreArchivo = "";
                if ($foto != "") {
                    $fecha = new DateTime();
                    $nombreArchivo = $fecha->getTimestamp() . "_" . $foto;
                    $tmpFoto = $_FILES["foto"]["tmp_name"];
                    if ($tmpFoto != "") {
                        move_uploaded_file($tmpFoto, "../../../images/clientes/" . $nombreArchivo);
                    }
                }

                // Insertar en la base de datos
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $id_rol = 5; // Rol fijo para clientes
                $sql = "INSERT INTO usuarios (dni, nombres, apellidos, correo, telefono, direccion, genero, password, id_rol, foto) 
                        VALUES (:dni, :nombres, :apellidos, :correo, :telefono, :direccion, :genero, :password, :id_rol, :foto)";
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindParam(":dni", $dni);
                $sentencia->bindParam(":nombres", $nombres);
                $sentencia->bindParam(":apellidos", $apellidos);
                $sentencia->bindParam(":correo", $correo);
                $sentencia->bindParam(":telefono", $telefono);
                $sentencia->bindParam(":direccion", $direccion);
                $sentencia->bindParam(":genero", $genero);
                $sentencia->bindParam(":password", $hashedPassword);
                $sentencia->bindParam(":id_rol", $id_rol);
                $sentencia->bindParam(":foto", $nombreArchivo);
                $sentencia->execute();

                header("Location:index.php");
            }
        } catch (PDOException $e) {
            $mensajeError = "Error en la base de datos: " . $e->getMessage();
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-2-fill"></i>
            Crear Nuevo <span class="text_red">Cliente</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="index.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Atras
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <?php if (isset($mensajeError)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $mensajeError; ?>
            </div>
        <?php } ?>

        <form id="form_cliente" action="" method="post" enctype="multipart/form-data">
    <div class="row">
        <!-- DNI -->
        <div class="col-md-6 col-12 mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input type="text"
                placeholder="Ingrese DNI"
                class="form-control" 
                name="dni" 
                id="dni" 
                value="<?php echo isset($_POST['dni']) ? $_POST['dni'] : ''; ?>" 
                required 
                minlength="8" 
                maxlength="8" 
                pattern="\d{8}" 
                title="El DNI debe contener exactamente 8 dígitos." />
        </div>

        <!-- Nombres -->
        <div class="col-md-6 col-12 mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input type="text" class="form-control" name="nombres" id="nombres" value="<?php echo isset($_POST['nombres']) ? $_POST['nombres'] : ''; ?>" placeholder="Ingrese nombres" required />
        </div>

        <!-- Apellidos -->
        <div class="col-md-6 col-12 mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input type="text" class="form-control" name="apellidos" id="apellidos" value="<?php echo isset($_POST['apellidos']) ? $_POST['apellidos'] : ''; ?>" placeholder="Ingrese apellidos" required />
        </div>

        <!-- Correo -->
        <div class="col-md-6 col-12 mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" id="correo" value="<?php echo isset($_POST['correo']) ? $_POST['correo'] : ''; ?>" placeholder="Ingrese correo" required />
        </div>

        <!-- Teléfono -->
        <div class="col-md-6 col-12 mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input 
                type="tel"
                placeholder="Ingrese teléfono"
                class="form-control" 
                name="telefono" 
                id="telefono" 
                value="<?php echo isset($_POST['telefono']) ? $_POST['telefono'] : ''; ?>" 
                required 
                minlength="9" 
                maxlength="9" 
                pattern="9\d{8}" 
                title="El teléfono debe iniciar con '9' seguido de 8 dígitos." 
            />
        </div>

        <!-- Dirección -->
        <div class="col-md-6 col-12 mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" name="direccion" id="direccion" value="<?php echo isset($_POST['direccion']) ? $_POST['direccion'] : ''; ?>" placeholder="Ingrese dirección" required />
        </div>

        <!-- Género -->
        <div class="col-md-6 col-12 mb-3">
            <label for="genero" class="form-label">Género</label>
            <select class="form-select" name="genero" id="genero" required>
                <option value="">Seleccionar</option>
                <option value="Masculino" <?php echo isset($_POST['genero']) && $_POST['genero'] == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                <option value="Femenino" <?php echo isset($_POST['genero']) && $_POST['genero'] == 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                <option value="Otro" <?php echo isset($_POST['genero']) && $_POST['genero'] == 'Otro' ? 'selected' : ''; ?>>Otro</option>
            </select>
        </div>

        <!-- Contraseña -->
        <div class="col-md-6 col-12 mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" id="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ''; ?>" placeholder="Ingrese contraseña" minlength="6" 
            title="La contraseña debe tener al menos 6 caracteres." required />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="col-md-6 col-12 mb-3">
            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" name="confirm_password" id="confirm_password" value="<?php echo isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; ?>" placeholder="Confirmar contraseña" minlength="6" 
            title="La contraseña debe tener al menos 6 caracteres." required />
        </div>

        <!-- Foto -->
        <div class="col-md-6 col-12 mb-3">
            <label for="foto" class="form-label">Foto (Opcional, máx. 3MB)</label>
            <input type="file" class="form-control" name="foto" id="foto" accept="image/*" />
        </div>
    </div>

    <div class="d-flex justify-content-start gap-3">
        <button type="submit" class="btn btn-success">
            <i class="ri-save-3-fill"></i> Crear Cliente
        </button>
        <a href="index.php" class="btn btn-danger">
            <i class="ri-close-line"></i> Cancelar
        </a>
    </div>
</form>

    </div>
</div>
<?php include("../../templates/footer_admin.php"); ?>
