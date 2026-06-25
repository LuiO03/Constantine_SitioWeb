<?php 
    include("../inicio/conexion.php");

    //============== OBTENER ID DE LA RED SOCIAL A EDITAR ==============//
    $txtID = isset($_GET['txtID']) ? intval($_GET['txtID']) : 0;

    if ($txtID <= 0) {
        header("Location: index.php");
        exit();
    }

    //============== CONSULTA PARA OBTENER DATOS DE LA RED SOCIAL ==============//
    $sql = "SELECT * FROM redes_sociales WHERE id_red = :id";
    $sentencia = $conexion->prepare($sql);
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $redSocial = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!$redSocial) {
        header("Location: index.php");
        exit();
    }

    if ($_POST) {
        $titulo = isset($_POST["titulo"]) ? htmlspecialchars(trim($_POST["titulo"])) : "";
        $descripcion = isset($_POST["descripcion"]) ? htmlspecialchars(trim($_POST["descripcion"])) : "";
        $enlace = isset($_POST["enlace"]) ? htmlspecialchars(trim($_POST["enlace"])) : "";
        $icono = isset($_POST["icono"]) ? htmlspecialchars(trim($_POST["icono"])) : "";
        $estado = isset($_POST["estado"]) ? intval($_POST["estado"]) : 1;

        // Validaciones del lado del servidor
        if (empty($titulo) || empty($descripcion) || empty($enlace)) {
            $mensajeError = "Todos los campos son obligatorios.";
        } elseif (!filter_var($enlace, FILTER_VALIDATE_URL)) {
            $mensajeError = "El enlace proporcionado no es válido.";
        } else {
            try {
                // Actualizar los datos de la red social
                $sql = "UPDATE redes_sociales SET 
                            titulo = :titulo, 
                            descripcion = :descripcion, 
                            enlace = :enlace, 
                            icono = :icono, 
                            estado = :estado 
                        WHERE id_red = :id";
                $sentencia = $conexion->prepare($sql);
                $sentencia->bindParam(":titulo", $titulo);
                $sentencia->bindParam(":descripcion", $descripcion);
                $sentencia->bindParam(":enlace", $enlace);
                $sentencia->bindParam(":icono", $icono);
                $sentencia->bindParam(":estado", $estado);
                $sentencia->bindParam(":id", $txtID);
                $sentencia->execute();

                header("Location: index.php");
                exit();
            } catch (PDOException $e) {
                $mensajeError = "Error al actualizar: " . $e->getMessage();
            }
        }
    }

    include("../../templates/header_admin.php");
?>

<div class="card shadow-sm mt-4 w-80 mx-auto rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria titulo_redes d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-global-line"></i>
            Editar Red Social: <span class="text_red">&nbsp; N° <?php echo $redSocial['id_red']; ?> ~ <?php echo htmlspecialchars($redSocial['titulo']); ?></span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="index.php" role="button">
            <i class="ri-arrow-left-s-line"></i> Atrás
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <?php if (isset($mensajeError)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $mensajeError; ?>
            </div>
        <?php } ?>

        <form id="form_red_social" action="" method="post">
    <div class="row">
        <div class="col-md-6 col-12 mb-3">
            <label for="red" class="form-label">
                <i class="ri-global-line"></i> Red Social
            </label>
            <select class="form-select" name="red" id="red" onchange="actualizarRedIcono()">
                <?php 
                    $redes = [
                        "Facebook" => "ri-facebook-circle-fill",
                        "Twitter" => "ri-twitter-fill",
                        "Instagram" => "ri-instagram-fill",
                        "LinkedIn" => "ri-linkedin-fill",
                        "YouTube" => "ri-youtube-fill",
                        "TikTok" => "ri-tiktok-fill",
                        "WhatsApp" => "ri-whatsapp-fill"
                    ];
                    foreach ($redes as $nombre => $icono) {
                        $selected = ($redSocial['icono'] == $icono) ? "selected" : "";
                        echo "<option value='{$nombre}' data-icon='{$icono}' {$selected}>{$nombre}</option>";
                    }
                ?>
            </select>
        </div>

        <input type="hidden" name="icono" id="icono" value="<?php echo htmlspecialchars($redSocial['icono']); ?>" />

        <div class="col-md-6 col-12 mb-3">
            <label for="titulo" class="form-label">
                <i class="ri-price-tag-3-fill"></i> Título
            </label>
            <input type="text" 
                   class="form-control" 
                   name="titulo" 
                   id="titulo" 
                   placeholder="Nombre de la red social" 
                   value="<?php echo htmlspecialchars($redSocial['titulo']); ?>" 
                   required 
                   pattern="^[a-zA-Z0-9\s]{3,50}$" 
                   maxlength="50" 
                   minlength="3" 
                   title="Solo letras, números y espacios. Mínimo 3 caracteres y máximo 50." />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="descripcion" class="form-label">
                <i class="ri-chat-smile-3-line"></i> Descripción
            </label>
            <textarea class="form-control" 
                      name="descripcion" 
                      id="descripcion" 
                      placeholder="Breve descripción" 
                      required 
                      maxlength="150" 
                      minlength="10" 
                      title="Debe contener entre 10 y 150 caracteres."><?php echo htmlspecialchars($redSocial['descripcion']); ?></textarea>
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="enlace" class="form-label">
                <i class="ri-link"></i> Enlace
            </label>
            <input type="url" 
                   class="form-control" 
                   name="enlace" 
                   id="enlace" 
                   placeholder="URL de la red social" 
                   value="<?php echo htmlspecialchars($redSocial['enlace']); ?>" 
                   required 
                   maxlength="255" 
                   pattern="https?://.+"
                   title="Debe ser un enlace válido que comience con http:// o https://" />
        </div>

        <div class="col-md-6 col-12 mb-3">
            <label for="estado" class="form-label">
                <i class="ri-checkbox-line"></i> Estado
            </label>
            <select class="form-select" name="estado" id="estado">
                <option value="1" <?php echo ($redSocial['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo ($redSocial['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
            </select>
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
        <small>Gestiona tus redes sociales de forma eficiente</small>
    </div>
</div>

<script>
    function actualizarRedIcono() {
        const redSelect = document.getElementById('red');
        const selectedOption = redSelect.options[redSelect.selectedIndex];
        document.getElementById('icono').value = selectedOption.getAttribute('data-icon');
    }
</script>

<?php include("../../templates/footer_admin.php"); ?>
