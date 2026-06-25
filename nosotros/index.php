<?php 
   include("../admin/seccion/inicio/conexion.php");

   $sentencia=$conexion->prepare("SELECT * FROM banners WHERE pagina = 'nosotros' AND estado = 1;");
   $sentencia->execute();
   $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);

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
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Constantine - Nosotros</title>
   <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
   <link rel="stylesheet" href="../style/nav.css">
   <link rel="stylesheet" href="../style/base.css">
   <link rel="stylesheet" href="../style/footer.css">
   <link rel="stylesheet" href="nosotros.css">
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
                  ¿QUIENES <span class="text_red">SOMOS?</span>
               </div>
               <div class="datos_contacto">
                  <p class="parrafo_cuerpo">
                     En Constantine, nos dedicamos a ofrecerte moda de calidad que celebra tu estilo único. 
                     Nuestra pasión es brindarte prendas que te hagan sentir y lucir increíble en cada ocasión.
                  </p>
                  <p class="parrafo_cuerpo">
                     Somos una tienda de ropa comprometida con la pasión por un estilo de vida activo. 
                     Trabajamos con las mejores marcas y diseñadores para ofrecer una amplia gama de productos que incluyen camisetas, pantalones, zapatillas, accesorios y más.
                  </p>
                  <p class="parrafo_cuerpo">
                     En nuestra tienda, creemos en la importancia de la comodidad, la calidad y el rendimiento. Cada artículo que ofrecemos ha sido cuidadosamente seleccionado para garantizar que cumple con nuestros estándares exigentes y satisface las necesidades de nuestros clientes apasionados por el deporte.
                  </p>
               </div>
            </article>

            <article class="imagen_seccion">
               <img src="img/somos.png" alt="constantine">
            </article>

         </div>
      </section>
      
      <section class="historia bloque_completo1">
         <div class="historia_contenedor">
            <div class="titulo_cuerpo">
               <span class="text_white">CONOCE NUESTRA</span> <span class="text_red">HISTORIA</span>
            </div>
            <div class="subtitulo_cuerpo">
               <span class="text_white">
                  Envíenos sus dudas y comentarios, nos pondremos en contacto con usted.
               </span>
            </div>
   
            <article class="historia_cuerpo">
               <p class="parrafo_cuerpo text_white">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia accusamus pariatur exercitationem delectus commodi accusantium eos voluptate, numquam incidunt ut itaque, perferendis deleniti magni suscipit eligendi iste facere veniam quis.
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Ut quas saepe illum odit nesciunt! Ab enim cupiditate asperiores molestias ipsam, consequatur quo nihil corporis dicta dignissimos blanditiis, est, recusandae sunt.
               </p>
               <p class="parrafo_cuerpo text_white">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia accusamus pariatur exercitationem delectus commodi accusantium eos voluptate, numquam incidunt ut itaque, perferendis deleniti magni suscipit eligendi iste facere veniam quis.
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Ut quas saepe illum odit nesciunt! Ab enim cupiditate asperiores molestias ipsam, consequatur quo nihil corporis dicta dignissimos blanditiis, est, recusandae sunt.
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Eos nisi facilis impedit quam iusto aspernatur nihil eius veritatis debitis itaque consequuntur quos perspiciatis facere aliquam non, ratione autem accusamus dolore?
               </p>
            </article>
         </div>
      </section>

      <!-- mision y vision -->  
      <section class="mision_vision bloque_1">
         <div class="sub_bloque">
            <!-- Lado Izquierdo con la Imagen -->
            <article class="lado_izquierdo">
               <img src="img/misionyvision.png" alt="Imagen sobre nosotros"/>
            </article>

            <!-- Lado Derecho con el Texto -->
            <article class="lado_derecho">

               <div class="contenido_imagen">
                  <i class="ri-fire-line"></i>
                  
                  <div class="titulo_cuerpo">
                     NUESTRA <span class="text_red">MISIÓN</span>
                  </div>
                  <p class="parrafo_cuerpo">
                     Ayudar a las empresas a conectarse con sus audiencias de manera significativa, creando experiencias memorables y resultados medibles.
                  </p>
               </div>

               <div class="contenido_imagen">
                  
                  <i class="ri-lightbulb-flash-line"></i>
                  <div class="titulo_cuerpo">
                     NUESTRA <span class="text_red">VISIÓN</span>
                  </div>
                  <p class="parrafo_cuerpo">
                     Convertirnos en líderes innovadores en el mundo del marketing y la publicidad, capacitando a las empresas para que alcancen su máximo potencial.
                  </p>
               </div>

            </article>

         </div>
      </section>

      <?php include("../layout/formulario.php"); ?>

   </main>

   <!--=============== FOOTER ===============-->
   <?php include("../layout/footer.php"); ?>