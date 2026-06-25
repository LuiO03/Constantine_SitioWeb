<?php 
   include("../admin/seccion/inicio/conexion.php");

   $sentencia=$conexion->prepare("SELECT * FROM banners WHERE pagina = 'servicios' AND estado = 1;");
   $sentencia->execute();
   $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);

   $sentencia=$conexion->prepare("SELECT * FROM servicio;");
   $sentencia->execute();
   $lista_servicios= $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constantine - Servicios</title>
    <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
    <link rel="stylesheet" href="../style/nav.css">
    <link rel="stylesheet" href="../style/base.css">
    <link rel="stylesheet" href="../style/footer.css">
    <link rel="stylesheet" href="servicios.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

   <!--=============== HEADER ===============-->
   <?php include("../layout/header.php"); ?>

    <main>
        <?php foreach($lista_banners as $banner) {
            $rutaImagen = '../images/banners/' . $banner['imagen'];

            if (!file_exists($rutaImagen) || empty($banner['imagen'])) {
                $rutaImagen = '../images/banners/banner_default.jpg';
            }
        ?>
            <section class="banner_contenido" style="background-image: url('<?php echo $rutaImagen; ?>');">
                <div class="banner_categoria">
                    <div class="titulo_banner"><?php echo $banner['titulo']; ?></div>
                    <div class="enfasis_banner"><?php echo $banner['enfasis']; ?></div>
                    <div class="descripcion_banner"><?php echo $banner['descripcion']; ?></div>
                </div>
            </section>
        <?php } ?>

        <!-- Primer contenedor -->
        <section class="bloque_1">
            <div class="sub_bloque">

                <div class="titulo_cuerpo">
                    Descubra lo  
                    <span class="text_red">que ofrecemos</span>
                </div>
                <div class="subtitulo_cuerpo">
                    Hacemos que cada prenda sea única. Ofrecemos estampados personalizados en una amplia variedad de prendas y diseñamos camisetas deportivas personalizadas para equipos o eventos especiales.
                </div>

                <!-- Servicio 1: Estampados Personalizados -->
                <?php foreach($lista_servicios as $servicio) {?>
                    <div class="service-item">
                        <div class="service-content">
                            <div class="service-text">
                                <h2><?php echo $servicio['nombre_servicio']; ?></h2>
                                <p class="parrafo_cuerpo"> <?php echo $servicio['descripcion']; ?></p>
                                <ul>
                                    <li><?php echo $servicio['detalle_1']; ?></li>
                                    <li><?php echo $servicio['detalle_2']; ?></li>
                                    <li><?php echo $servicio['detalle_3']; ?></li>
                                </ul>
                                <p class="benefit"><?php echo $servicio['frase']; ?></p>
                            </div>
                            <div class="service-image">
                                <img src="../images/servicios/<?php echo $servicio['imagen']; ?>" alt="Estampados personalizados">
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- Proceso de Personalización -->
                <div class="customization-process">
                    <h2>¿Cómo Funciona?</h2>
                    <ol>
                        <li>Elige tu prenda o camiseta deportiva.</li>
                        <li>Envía tu diseño o idea, o solicita ayuda con el diseño.</li>
                        <li>Revisión del diseño y ajustes antes de la producción.</li>
                        <li>Confirmación y producción del estampado.</li>
                        <li>Entrega rápida o recogida en tienda.</li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="imagen_fondo">
            <div class="contenido_centrado">
                <div class="titulo_cuerpo">
                    ¿Te interesan nuestros <span class="text_red">servicios?</span>
                </div>
                <img src="img/icon.png" alt="Imagen Pequeña" class="imagen-pequena">

                <div class="titulo_cuerpo text_red" style="font-size: 30px;">
                    Días de Descuentos de hasta el 10%
                </div>

                <p class="parrafo_cuerpo">
                    Todos los jueves, de 4 p.m. a 7 p.m.
                </p>

                <button class="boton_contenedor">
                    <button class="boton_general">
                        <span>
                            CONTÁCTANOS
                        </span>
                    </button>
                </button>
            </div>
        </section>

   </main>
   
   <!--=============== FOOTER ===============-->
   <?php include("../layout/footer.php"); ?>