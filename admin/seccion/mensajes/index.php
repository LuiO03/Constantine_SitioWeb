<?php
    include("../inicio/conexion.php");

    //============== CONSULTA TABLA FORMULARIO DE CONTACTO ==============//
    $sentencia = $conexion->prepare("SELECT * FROM formulario_contacto");
    $sentencia->execute();
    $listaContactos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaContactos as &$contacto) {
        foreach ($contacto as $key => $value) {
            $contacto[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($contacto); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm d-none d-lg-block rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-mail-send-line"></i>
            Administrar <span class="text_red">Formulario de Contacto</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i> Página Principal
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="table-responsive">
            <table class="table table-sm table-light table-bordered table-hover align-middle table-striped">
                <thead>
                    <tr>
                        <th class="w-10 text-center">ID</th>
                        <th class="w-20 text-center">Nombre</th>
                        <th class="w-20 text-center">Correo</th>
                        <th class="w-20 text-center">Teléfono</th>
                        <th class="w-30 text-center">Mensaje</th>
                        <th class="w-10 text-center">F.Envío</th>
                        <th class="w-20 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($listaContactos as $contacto) { ?>
                        <tr class="align-middle">
                            <td scope="row" class="text-center"><?php echo $contacto['id_form_contacto']; ?></td>
                            <td><?php echo $contacto['nombres'] . " " . $contacto['apellidos']; ?></td>
                            <td><?php echo $contacto['correo']; ?></td>
                            <td class="text-center"><?php echo $contacto['telefono']; ?></td>
                            <td><?php echo nl2br($contacto['mensaje']); ?></td>
                            <td class="text-center"><?php echo date("d/m/Y H:i", strtotime($contacto['fecha_envio'])); ?></td>
                            <td class="align-middle text-center">
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $contacto['id_form_contacto']; ?>)">
                                    <i class="ri-delete-bin-2-fill"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona los formularios de contacto de forma eficiente</small>
    </div>
</div>

<!-- LISTA DE REGISTROS PARA MÓVIL -->
<div id="ventana_movil" class="card shadow-sm d-block d-lg-none rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-mail-send-line"></i>
            Administrar <span class="text_red">Formulario de Contacto</span>
        </span>
        <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
            <i class="ri-arrow-left-s-line"></i>
        </a>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row">
            <?php foreach($listaContactos as $contacto) { ?>
                <div class="col-12 mb-3">
                    <div class="card p-3" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="ri-hashtag"></i> ID:</strong> <?php echo $contacto['id_form_contacto']; ?><br>
                                <strong><i class="ri-user-line"></i> Nombre:</strong> <?php echo $contacto['nombres'] . " " . $contacto['apellidos']; ?><br>
                                <strong><i class="ri-mail-line"></i> Correo:</strong> <?php echo $contacto['correo']; ?><br>
                                <strong><i class="ri-phone-line"></i> Teléfono:</strong> <?php echo $contacto['telefono']; ?><br>
                                <strong><i class="ri-message-3-line"></i> Mensaje:</strong> <?php echo nl2br($contacto['mensaje']); ?><br>
                                <strong><i class="ri-calendar-line"></i> Fecha de Envío:</strong> <?php echo date("d/m/Y H:i", strtotime($contacto['fecha_envio'])); ?><br>
                            </div>
                            <div class="d-flex flex-column align-items-end gap-2">
                                <button <?php echo $permiso['eliminar']; ?> class="btn btn-danger btn-sm mx-1" onclick="confirmDelete(<?php echo $contacto['id_form_contacto']; ?>)" style="min-width: 40px; min-height: 40px;">
                                    <i class="ri-delete-bin-2-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona los formularios de contacto de forma eficiente</small>
    </div>
</div>
<!-- BOTONES FLOTANTES EN LA PANTALLA -->
<div class="main_botones_flotantes">
        <div class="botones_fixed">
            <li>
                <a class="boton_pdf" href="generar-pdf.php">
                    <i class="ri-file-pdf-fill"></i>
                </a>
            </li>
            <li>
                <a class="boton_excel" href="generar-excel.php">
                    <i class="ri-file-excel-fill"></i>
                </a>
            </li>
           
        </div>
    </div>

<?php include("../../templates/footer_admin.php"); ?>
