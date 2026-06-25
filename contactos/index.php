<?php 
   include("../admin/seccion/inicio/conexion.php");

   $sentencia = $conexion->prepare("SELECT * FROM banners WHERE pagina = 'contactos' AND estado = 1;");
   $sentencia->execute();
   $lista_banners = $sentencia->fetchAll(PDO::FETCH_ASSOC);

   // Comprobar si el formulario fue enviado
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Recoger los datos del formulario
      $nombres = htmlspecialchars($_POST['nombres'], ENT_QUOTES, 'UTF-8');
      $apellidos = htmlspecialchars($_POST['apellidos'], ENT_QUOTES, 'UTF-8');
      $telefono = htmlspecialchars($_POST['telefono'], ENT_QUOTES, 'UTF-8');
      $correo = htmlspecialchars($_POST['correo'], ENT_QUOTES, 'UTF-8');
      $mensaje = htmlspecialchars($_POST['mensaje'], ENT_QUOTES, 'UTF-8');
      $terminos = isset($_POST['terminos']) ? 1 : 0; // Verifica si se aceptaron los términos

      // Preparar la consulta SQL para insertar los datos en la tabla
      $sql = "INSERT INTO formulario_contacto (nombres, apellidos, telefono, correo, mensaje, terminos)
              VALUES (:nombres, :apellidos, :telefono, :correo, :mensaje, :terminos)";
      
      // Preparar la sentencia
      $stmt = $conexion->prepare($sql);

      // Vincular los parámetros
      $stmt->bindParam(':nombres', $nombres);
      $stmt->bindParam(':apellidos', $apellidos);
      $stmt->bindParam(':telefono', $telefono);
      $stmt->bindParam(':correo', $correo);
      $stmt->bindParam(':mensaje', $mensaje);
      $stmt->bindParam(':terminos', $terminos);

      // Ejecutar la sentencia
      if ($stmt->execute()) {
         echo "Formulario enviado con éxito.";
      } else {
         echo "Error al enviar el formulario.";
      }
   }

   $sentencia = $conexion->prepare("SELECT * FROM locales ");
   $sentencia->execute();
   $lista_locales = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Constantine - Contactos</title>
   <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
   <link rel="stylesheet" href="../style/nav.css">
   <link rel="stylesheet" href="../style/base.css">
   <link rel="stylesheet" href="../style/footer.css">
   <link rel="stylesheet" href="contacto.css">
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

      <!-- Introducción -->
      <section class="introduccion bloque_1">

         <div class="sub_bloque">
            <article>
               <div class="titulo_cuerpo">
                  QUE ESPERAS, <span class="text_red">VISÍTANOS</span>
               </div>
               <div class="datos_contacto">
                  <p class="parrafo_cuerpo">
                     Estamos encantados de tenerte aquí. En Constantine, nos dedicamos a ofrecerte la mejor experiencia posible y estamos aquí para ayudarte con cualquier consulta o necesidad que tengas. 
                  </p>
                  <p class="parrafo_cuerpo">
                     No dudes en ponerte en contacto con nosotros a través de este formulario. Nuestro equipo de atención al cliente está siempre listo para asistirte y asegurar que recibas una respuesta rápida y eficiente. 
                  </p>
               </div>
            </article>
            <article class="introduccion_imagen">
               <img src="img/local_imagen1.jpg" alt="Foto tienda">
            </article>
            <article class="introduccion_imagen">
               <img src="img/local_imagen2.jpg" alt="Foto tienda">
            </article>

         </div>
         
      </section>

      <article class="mapa_seccion">
         <div class="titulo_cuerpo">
            NUESTRA <span class="text_red">UBICACIÓN</span>
         </div>
         <div class="datos_contacto">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15623.167421630069!2d-75.50590870216061!3d-11.779701121091026!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xb48b4130b6c81e02!2sForest%20Expeditions%20Per%C3%BA!5e0!3m2!1ses!2spe!4v1668456702837!5m2!1ses!2spe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>
         </div>
      </article>

      <!--=============== DIRECCIONES ===============-->
      <section class="ayuda__container bloque_completo2">

         <div class="titulo_cuerpo">ESTAMOS PARA <span class="text_red">AYUDARTE</span></div>
            <div class="subtitulo_cuerpo">
               <span class="text_white">
                  Solucionaremos tu caso lo más pronto posible en cualquiera de los siguientes canales. 
               </span>
            </div>
            <div class="ayudas sub_bloque2">
               <?php foreach($contactos_negocio as $contacto) {?>

                  <div class="ayuda">
                     <div class="icono">
                           <i class="ri-time-line"></i>
                     </div>
                     <h4 class="titulo_lista">HORA DE ATENCIÓN</h4>
                     <p class="parrafo_cuerpo">
                        <?php echo $contacto['hora_atencion']; ?>
                     </p>
                  </div>

                  <div class="ayuda">
                     <div class="icono">
                           <i class="ri-map-pin-line"></i> <!-- Icono de ubicación -->
                     </div>
                     <h4 class="titulo_lista">NUESTRA DIRECCIÓN</h4>
                     <p class="parrafo_cuerpo">
                        <?php echo $contacto['direccion']; ?>
                     </p>
                  </div>

                  <div class="ayuda">
                     <div class="icono">
                           <i class="ri-mail-line"></i> <!-- Icono de correo -->
                     </div>
                     <h4 class="titulo_lista">ESCRÍBENOS UN CORREO</h4>
                     <p class="parrafo_cuerpo">
                        <?php echo $contacto['correo']; ?>
                     </p>
                  </div>

                  <div class="ayuda">
                     <div class="icono">
                           <i class="ri-phone-line"></i> <!-- Icono de teléfono -->
                     </div>
                     <h4 class="titulo_lista">LLÁMANOS AL NÚMERO</h4>
                     <p class="parrafo_cuerpo">
                        <?php echo $contacto['telefono']; ?> <br>
                        <?php echo $contacto['celular']; ?>
                     </p>
                  </div>
               <?php } ?>
            </div>
      </section>

      <!--=============== REDES SOCIALES ===============-->
      <section class="redes_sociales__container bloque_1">
         <div class="titulo_cuerpo">SÍGANOS EN NUESTRAS <span class="text_red">REDES SOCIALES</span></div>
         <div class="subtitulo_cuerpo">
            Conéctate con nosotros a través de nuestras plataformas sociales y mantente actualizado con las últimas novedades.
         </div>
         
         <div class="redes sub_bloque2">
            <?php foreach ($redes_sociales as $red) {?>
               <a href="<?php echo $red['enlace']?>" class="red <?php echo $red['red']?>">
                  <div class="icono_red">
                        <i class="<?php echo $red['icono']?>"></i>
                  </div>
                  <h4 class="titulo_lista"><?php echo $red['titulo']?></h4>
                  <p class="red_descripcion"><?php echo $red['descripcion']?></p>
               </a>
            <?php }?>
         </div>
      </section>

      <?php include("../layout/formulario.php"); ?>
   
      <!--=============== LOCALES ===============-->
      <section class="contacto__locales bloque_1">
         <div class="titulo_cuerpo">CONOZCA NUESTROS <span class="text_red">LOCALES</span></div>
         <div class="locales sub_bloque2">

            <?php foreach($lista_locales as $local) {?>
               
               <div class="local_card">
                  <div class="local_img">
                     <img src="<?php echo $url_base;?>/images/locales/<?php echo $local['imagen'];?>" alt="Imagen Local 1">
                  </div>
                  <div class="local_info">
                     <h4><i class="ri-map-pin-line"></i> <?php  echo $local['nombre_local']; ?></h4>
                     <p><i class="ri-map-pin-line"></i> Dirección: <?php  echo $local['direccion']; ?></p>
                     <p><i class="ri-time-line"></i> Horario de Atención: <?php  echo $local['horario']; ?></p>
                     <p><i class="ri-phone-line"></i> Teléfono: <?php  echo $local['telefono']; ?></p>
                     <a href="<?php  echo $local['enlace']; ?>" target="_blank" class="local_btn">
                        <div>
                           <i class="ri-map-2-line"></i> Ver en Google Maps
                        </div>
                     </a>
                  </div>
               </div>

            <?php } ?>
         </div>
      </section>
   </main>

   <!--=============== FOOTER ===============-->
   <?php include("../layout/footer.php"); ?>
   
   