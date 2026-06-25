<?php 
    include("../inicio/conexion.php");

    // Obtener todos los roles disponibles
    $sqlRoles = "SELECT * FROM roles";
    $sentenciaRoles = $conexion->prepare($sqlRoles);
    $sentenciaRoles->execute();
    $roles = $sentenciaRoles->fetchAll(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura de los datos del formulario
        $dni = trim($_POST["dni"] ?? "");
        $nombres = trim($_POST["nombres"] ?? "");
        $apellidos = trim($_POST["apellidos"] ?? "");
        $correo = trim($_POST["correo"] ?? "");
        $telefono = trim($_POST["telefono"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";
        $id_rol = $_POST["id_rol"] ?? "";
        $foto = $_FILES["foto"]["name"] ?? "";
        $pesoImagen = $_FILES["foto"]["size"] ?? 0;

        // Validaciones adicionales
        if (!preg_match('/^\d{8}$/', $dni)) {
            $mensajeError = "El DNI debe contener exactamente 8 dígitos.";
        } elseif (!preg_match('/^[A-Za-záéíóúÁÉÍÓÚ\s]+$/', $nombres) || strlen($nombres) > 50) {
            $mensajeError = "Los nombres solo deben contener letras y tener un máximo de 50 caracteres.";
        } elseif (!preg_match('/^[A-Za-záéíóúÁÉÍÓÚ\s]+$/', $apellidos) || strlen($apellidos) > 50) {
            $mensajeError = "Los apellidos solo deben contener letras y tener un máximo de 50 caracteres.";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $mensajeError = "El correo no tiene un formato válido.";
        } elseif (!preg_match('/^\d{9}$/', $telefono)) {
            $mensajeError = "El teléfono debe contener exactamente 9 dígitos.";
        } elseif (strlen($password) < 6) {
            $mensajeError = "La contraseña debe tener al menos 6 caracteres.";
        } elseif ($password !== $confirm_password) {
            $mensajeError = "Las contraseñas no coinciden.";
        } elseif ($pesoImagen > 3145728) {
            $mensajeError = "El tamaño de la imagen no debe superar los 3 MB.";
        } elseif (!in_array($id_rol, array_column($roles, 'id_rol'))) {
            $mensajeError = "El rol seleccionado no es válido.";
        } else {
            // Validar si el correo ya existe
            $sqlCorreo = "SELECT id_usuario FROM usuarios WHERE correo = :correo";
            $stmtCorreo = $conexion->prepare($sqlCorreo);
            $stmtCorreo->bindParam(":correo", $correo);
            $stmtCorreo->execute();
            if ($stmtCorreo->rowCount() > 0) {
                $mensajeError = "El correo ya está registrado.";
            } else {
                // Subir la imagen si fue seleccionada
                $nombreArchivo = "";
                if ($foto != "") {
                    $fecha = new DateTime();
                    $nombreArchivo = $fecha->getTimestamp() . "_" . $foto;
                    $tmpFoto = $_FILES["foto"]["tmp_name"];
                    if ($tmpFoto != "") {
                        move_uploaded_file($tmpFoto, "../../../images/usuarios/" . $nombreArchivo);
                    }
                }

                // Insertar los datos del nuevo usuario en la base de datos
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (dni, nombres, apellidos, correo, telefono, password, id_rol, foto) 
                        VALUES (:dni, :nombres, :apellidos, :correo, :telefono, :password, :id_rol, :foto)";
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindParam(":dni", $dni);
                $sentencia->bindParam(":nombres", $nombres);
                $sentencia->bindParam(":apellidos", $apellidos);
                $sentencia->bindParam(":correo", $correo);
                $sentencia->bindParam(":telefono", $telefono);
                $sentencia->bindParam(":password", $hashedPassword);
                $sentencia->bindParam(":id_rol", $id_rol);
                $sentencia->bindParam(":foto", $nombreArchivo);
                $sentencia->execute();

                header("Location:index.php");
                exit;
            }
        }
    }

    include("../../templates/header_admin.php");
?>


<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-2-fill"></i>
            Crear Nuevo <span class="text_red">Usuario</span>
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

        <form id="form_usuario" action="" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 col-12 mb-3">
            <label for="dni" class="form-label">DNI</label>
            <input 
                type="text" 
                class="form-control" 
                name="dni" 
                id="dni" 
                placeholder="Ingrese DNI" 
                pattern="^(?!12345678$)\d{8}$" 
                maxlength="8" 
                minlength="8" 
                value="<?php echo htmlspecialchars($dni ?? ''); ?>" 
                title="El DNI debe tener 8 dígitos y no puede ser 12345678." 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="nombres" class="form-label">Nombres</label>
            <input 
                type="text" 
                class="form-control" 
                name="nombres" 
                id="nombres" 
                placeholder="Ingrese nombres" 
                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" 
                maxlength="50" 
                title="Ingrese un nombre válido (solo letras y espacios)." 
                value="<?php echo htmlspecialchars($nombres ?? ''); ?>" 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="apellidos" class="form-label">Apellidos</label>
            <input 
                type="text" 
                class="form-control" 
                name="apellidos" 
                id="apellidos" 
                placeholder="Ingrese apellidos" 
                pattern="[A-Za-záéíóúÁÉÍÓÚñÑ\s]+" 
                maxlength="50" 
                title="Ingrese apellidos válidos (solo letras y espacios)." 
                value="<?php echo htmlspecialchars($apellidos ?? ''); ?>" 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input 
                type="email" 
                class="form-control" 
                name="correo" 
                id="correo" 
                placeholder="Ingrese correo" 
                maxlength="100" 
                title="Ingrese un correo válido." 
                value="<?php echo htmlspecialchars($correo ?? ''); ?>" 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input 
                type="tel" 
                class="form-control" 
                name="telefono" 
                id="telefono" 
                placeholder="Ingrese teléfono" 
                value="<?php echo htmlspecialchars($telefono ?? ''); ?>" 
                pattern="^9\d{8}$" 
                maxlength="9" 
                minlength="9" 
                title="El teléfono debe comenzar con 9 y tener 9 dígitos." 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="rol" class="form-label"><i class="ri-shield-user-line"></i> Rol</label>
            <select 
                class="form-select" 
                name="id_rol" 
                id="rol" 
                required>
                <option value="">Seleccionar</option>
                <?php foreach ($roles as $rol) { ?>
                    <option value="<?php echo $rol['id_rol']; ?>" 
                        <?php echo (isset($id_rol) && $id_rol == $rol['id_rol']) ? 'selected' : ''; ?>>
                        <?php echo $rol['rol_nombre']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input 
                type="password" 
                class="form-control" 
                name="password" 
                id="password" 
                placeholder="Ingrese contraseña" 
                minlength="6" 
                maxlength="20" 
                title="La contraseña debe tener entre 6 y 20 caracteres." 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
            <input 
                type="password" 
                class="form-control" 
                name="confirm_password" 
                id="confirm_password" 
                placeholder="Confirmar contraseña" 
                minlength="6" 
                maxlength="20" 
                title="Confirme la contraseña (entre 6 y 20 caracteres)." 
                required />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="foto" class="form-label">Foto (Máx. 3 MB)</label> 
            <input 
                type="file" 
                class="form-control" 
                name="foto" 
                id="foto" 
                accept="image/*" 
                onchange="previewImage(event)" 
                title="Seleccione una imagen válida de máximo 3 MB." />
        </div>

                <div class="col-md-6 col-12 mb-3 d-flex flex-column">
                    <label for="foto" class="form-label">Previsualización de la Foto:</label>
                    <img id="preview" src="" alt="Vista previa de la foto" class="img-fluid rounded border w-25" onclick="toggleImageSize()" />
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3">
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-save-3-fill"></i> Crear Usuario
                </button>

                <a name="cancelar" id="cancelar" class="btn btn-danger d-flex align-items-center gap-1" href="index.php" role="button">
                    <i class="ri-close-line"></i> Cancelar
                </a>
            </div>
        </form>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus usuarios de forma eficiente</small>
    </div>
</div>

<?php include("../../templates/footer_admin.php"); ?>