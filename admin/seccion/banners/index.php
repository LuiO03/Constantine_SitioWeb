<?php 
    include("../inicio/conexion.php");

    //============== CONSULTA TABLA BANNERS ==============//
    $sentencia = $conexion->prepare("SELECT * FROM banners");
    $sentencia->execute();
    $listaBanners = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    // Escapar los datos de la base de datos con htmlspecialchars
    foreach ($listaBanners as &$banner) {
        foreach ($banner as $key => $value) {
            $banner[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    unset($banner); // Evitar referencias no deseadas

    include("../../templates/header_admin.php");

    
?>

<!-- LISTA DE REGISTROS PARA ESCRITORIO -->
<div id="ventana_escritorio" class="card shadow-sm rounded-0">
    <div class="card-header d-flex justify-content-between align-items-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <span class="titulo_categoria d-flex align-items-center gap-1 flex-wrap">
            <i class="ri-image-fill"></i>
            Administrar <span class="text_red">Banners</span>
        </span>
        <div class="d-flex gap-2">
            <a name="atras" id="atras" class="btn btn-primary d-flex align-items-center gap-1" href="../inicio/" role="button">
                <i class="ri-arrow-left-s-line"></i> Página Principal <?php echo $permiso['leer']; ?>
            </a>
        </div>
    </div>

    <div class="card-body" style="color: var(--color_texto); background-color: var(--color_barra_lateral);">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach($listaBanners as $Banners) { ?>
                <div class="col">
                    <div class="card h-100" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
                        <div class="row g-0 h-100">
                            <!-- Imagen a la izquierda -->
                            <div class="col-6">
                                <a href="editar.php?txtID=<?php echo $Banners['id_banner']; ?>" class="text-decoration-none h-100 d-block">
                                    <?php
                                        $rutaImagen = "../../../images/banners/" . $Banners['imagen'];
                                    
                                        if (!file_exists($rutaImagen) || empty($Banners['imagen'])) {
                                            $rutaImagen = "../../../images/banners/banner_default.jpg";
                                        }
                                    ?>
                                    <img src="<?php echo $rutaImagen; ?>" class="img-fluid w-100 h-100 rounded-start" alt="<?php echo $Banners['pagina'] . $Banners['orden']; ?>" style="object-fit: cover;">
                                </a>
                            </div>

                            <!-- Información a la derecha -->
                            <div class="col-6 d-flex flex-column justify-content-between">
                                <div class="card-body">
                                    <h5 class="card-title text-uppercase mb-1">
                                        <?php echo $Banners["pagina"]; ?>
                                    </h5>
                                    <p class="card-text small mb-1">
                                        <strong># </strong><?php echo $Banners['orden']; ?>
                                        <td class="text-center">
                                            <?php if ($Banners['estado'] == 1) { ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php } else { ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php } ?>
                                        </td>
                                    </p>
                                    <p class="card-text small mb-1">
                                        <strong><?php echo $Banners["titulo"]; ?></strong>
                                    </p>
                                    <p class="card-text mb-1">
                                        <?php echo $Banners["enfasis"]; ?>
                                    </p>
                                </div>
                                <div class="p-2">
                                    <a class="btn btn-warning btn-sm w-100 <?php echo $permiso['editar']; ?>" href="editar.php?txtID=<?php echo $Banners['id_banner']; ?>" role="button">
                                        <i class="ri-pencil-fill"></i> Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="card-footer text-center rounded-0" style="color: var(--color_texto); background-color: var(--color_barra_superior);">
        <small>Gestiona tus banners de forma eficiente</small>
    </div>
</div>


<?php include("../../templates/footer_admin.php"); ?>

