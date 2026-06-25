<?php 
    include("../inicio/conexion.php");

    //============== OBTENER ID DEL USUARIO A EDITAR ==============//
    $txtID = isset($_GET['txtID']) ? $_GET['txtID'] : "";

    //============== CONSULTA PARA OBTENER DATOS DEL USUARIO ==============//
    $sql = "SELECT u.*, r.rol_nombre FROM usuarios u LEFT JOIN roles r ON u.id_rol = r.id_rol WHERE u.id_usuario = :id";
    
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

    // Obtener todos los roles disponibles
    $sqlRoles = "SELECT * FROM roles";
    $sentenciaRoles = $conexion->prepare($sqlRoles);
    $sentenciaRoles->execute();
    $roles = $sentenciaRoles->fetchAll(PDO::FETCH_ASSOC);

    if ($_POST) {
        // Captura de los datos editados del formulario
        $dni = isset($_POST["dni"]) ? $_POST["dni"] : "";
        $nombres = isset($_POST["nombres"]) ? $_POST["nombres"] : "";
        $apellidos = isset($_POST["apellidos"]) ? $_POST["apellidos"] : "";
        $correo = isset($_POST["correo"]) ? $_POST["correo"] : "";
        $telefono = isset($_POST["telefono"]) ? $_POST["telefono"] : "";
        $password = isset($_POST["password"]) ? $_POST["password"] : "";
        $confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : "";
        $id_rol = isset($_POST["id_rol"]) ? $_POST["id_rol"] : "";
        $estado = isset($_POST["estado"]) ? $_POST["estado"] : "";
        $foto = isset($_FILES["foto"]["name"]) ? $_FILES["foto"]["name"] : "";
        $pesoImagen = isset($_FILES["foto"]["size"]) ? $_FILES["foto"]["size"] : 0;

        if ($password !== $confirm_password) {
            $mensajeError = "Las contraseñas no coinciden.";
        } elseif ($pesoImagen > 3145728) {
            $mensajeError = "El tamaño de la imagen no debe superar los 3MB.";
        } else if ($dni && $nombres && $apellidos && $correo && $telefono) {
            // Actualizar los datos del usuario
            $sql = "UPDATE usuarios SET dni = :dni, nombres = :nombres, apellidos = :apellidos, correo = :correo, telefono = :telefono, id_rol = :id_rol, estado = :estado";
            if ($password) {
                $sql .= ", password = :password";
            }
            $sql .= " WHERE id_usuario = :id";
            $sentencia = $conexion->prepare($sql);
            $sentencia->bindParam(":dni", $dni);
            $sentencia->bindParam(":nombres", $nombres);
            $sentencia->bindParam(":apellidos", $apellidos);
            $sentencia->bindParam(":correo", $correo);
            $sentencia->bindParam(":telefono", $telefono);
            $sentencia->bindParam(":id_rol", $id_rol);
            $sentencia->bindParam(":estado", $estado);
            $sentencia->bindParam(":id", $txtID);
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sentencia->bindParam(":password", $hashedPassword);
            }
            $sentencia->execute();

            // Verificar si se subió una nueva imagen
            if ($foto != "") {
                if ($usuario["foto"] != "" && file_exists("../../../images/usuarios/" . $usuario["foto"])) {
                    unlink("../../../images/usuarios/" . $usuario["foto"]);
                }

                // Subir la nueva imagen
                $fecha = new DateTime();
                $nombreArchivo = $fecha->getTimestamp() . "_" . $foto;
                $tmpFoto = $_FILES["foto"]["tmp_name"];

                if ($tmpFoto != "") {
                    move_uploaded_file($tmpFoto, "../../../images/usuarios/" . $nombreArchivo);

                    // Actualizar la foto en la base de datos
                    $sql = "UPDATE usuarios SET foto = :foto WHERE id_usuario = :id";
                    $sentencia = $conexion->prepare($sql);
                    $sentencia->bindParam(":foto", $nombreArchivo);
                    $sentencia->bindParam(":id", $txtID);
                    $sentencia->execute();
                }
            }
            header("Location:index.php");
        } else {
            $mensajeError = "Todos los campos son obligatorios.";
        }
    }
    
    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-user-2-fill"></i>
            Editar Usuario: <span class="text_red">&nbsp; N° <?php echo $usuario['id_usuario']; ?> ~ <?php echo $usuario['nombres'] . ' ' . $usuario['apellidos']; ?></span>
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
                    <input type="text" class="form-control" name="dni" id="dni" value="<?php echo $usuario['dni']; ?>" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="nombres" class="form-label">Nombres</label>
                    <input type="text" class="form-control" name="nombres" id="nombres" value="<?php echo $usuario['nombres']; ?>" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="apellidos" class="form-label">Apellidos</label>
                    <input type="text" class="form-control" name="apellidos" id="apellidos" value="<?php echo $usuario['apellidos']; ?>" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" name="correo" id="correo" value="<?php echo $usuario['correo']; ?>" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" name="telefono" id="telefono" value="<?php echo $usuario['telefono']; ?>" required />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="rol" class="form-label"><i class="ri-shield-user-line"></i> Rol</label>
                    <select class="form-select" name="id_rol" id="rol" required>
                        <option value="">Seleccionar</option>
                        <?php foreach ($roles as $rol) { ?>
                            <option value="<?php echo $rol['id_rol']; ?>" <?php echo ($usuario['id_rol'] == $rol['id_rol']) ? 'selected' : ''; ?>>
                                <?php echo $rol['rol_nombre']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Dejar vacío para no cambiar" />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Dejar vacío para no cambiar" />
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" name="estado" id="estado" required>
                        <option value="1" <?php echo ($usuario['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($usuario['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>


                <div class="col-md-6 col-12 mb-3">
                    <label for="foto" class="form-label">Foto (Máx. 3 MB)</label> <input type="file" class="form-control" name="foto" id="foto" accept="image/*" onchange="previewImage(event)" /> 
                </div>

                <div class="col-md-6 col-12 mb-3 d-flex flex-column">
                    <label for="foto" class="form-label">Previsualización de la Foto:</label>
                    <img id="preview" src="../../../images/usuarios/<?php echo $usuario['foto']; ?>" alt="Vista previa de la foto" class="img-fluid rounded border w-25" onclick="toggleImageSize()" />
                </div>
            </div>

            <div class="d-flex justify-content-start gap-3">
                <button type="submit" class="btn btn-success d-flex align-items-center gap-1">
                    <i class="ri-save-3-fill"></i> Guardar Cambios
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
