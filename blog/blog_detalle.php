<?php 
   include("../admin/seccion/inicio/conexion.php");

   // Verificar si se ha pasado un ID válido por la URL
   if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $id_blog = intval($_GET['id']);
      
      // Consultar el blog específico basado en el ID
      $sentencia = $conexion->prepare("SELECT * FROM blogs WHERE id_blog = :id");
      $sentencia->bindParam(':id', $id_blog, PDO::PARAM_INT);
      $sentencia->execute();
      $blog = $sentencia->fetch(PDO::FETCH_ASSOC);

      // Si no se encuentra el blog, redirigir al índice o mostrar error
      if (!$blog) {
         header("Location: index.php");
         exit();
      }
   } else {
      // Redirigir si no hay ID válido
      header("Location: index.php");
      exit();
   }
?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($blog['titulo_blog']); ?> - Constantine</title>
   <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png">
   <link rel="stylesheet" href="../style/nav.css">
   <link rel="stylesheet" href="../style/base.css">
   <link rel="stylesheet" href="../style/footer.css">
   <link rel="stylesheet" href="blog_detalle.css">
   <link href="../libs/remix/fonts/remixicon.css" rel="stylesheet">

   <style>
      /* General styles */
      .contenedor {
         max-width: 1000px; /* Reducir el ancho máximo */
         margin: 0 auto; /* Centrar el contenido horizontalmente */
         padding: 20px;
      }

      .detalle_titulo {
         text-align: center;
         font-size: 2.5rem;
         margin-bottom: 20px;
         font-weight: normal; /* Quitar negrita del título */
      }

      .detalle_contenido {
         display: flex;
         flex-direction: row;
         align-items: flex-start;
         gap: 20px;
         margin-top: 50px;
      }

      .detalle_texto {
         text-align: left;
         flex: 1;
      }

      .detalle_img img {
         max-width: 400px; /* Reducir el tamaño de la imagen */
         height: auto;
         display: block;
      }

      /* Solo la categoría en color rojo */
      .detalle_categoria {
         color: red; /* Cambiar color de la categoría */
         font-weight: normal; /* Sin negrita */
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
         .detalle_contenido {
            flex-direction: column;
            align-items: center; /* Centrar contenido en pantallas pequeñas */
         }

         .detalle_img {
            margin-top: 20px;
            text-align: center;
         }

         .detalle_img img {
            max-width: 90%; /* Imagen más pequeña en pantallas pequeñas */
         }

         .contenedor {
            padding: 15px;
         }
      }
   </style>
</head>
<body>
   
   <!--=============== HEADER ===============-->
   <?php include("../layout/header.php"); ?>

   <main>
      <section class="blog_detalle">
         <div class="contenedor">
            <h1 class="detalle_titulo"><?php echo htmlspecialchars($blog['titulo_blog']); ?></h1>
            <div class="detalle_contenido">
               <!-- Contenido al lado izquierdo -->
               <div class="detalle_texto">
                  <p class="detalle_categoria">
                     <i class="ri-<?php echo strtolower($blog['categoria']); ?>-line"></i> 
                     <?php echo htmlspecialchars($blog['categoria']); ?>
                  </p>
                  <?php echo nl2br($blog['contenido']); ?>
               </div>
               <!-- Imagen al lado derecho -->
               <div class="detalle_img">
                  <img src="img/<?php echo htmlspecialchars($blog['imagen']); ?>" 
                       alt="<?php echo htmlspecialchars($blog['titulo_blog']); ?>">
               </div>
            </div>
         </div>
      </section>

      <!-- Botón para regresar -->
      <div class="contenedor">
         <a href="index.php" class="boton_regresar">← Regresar al Blog</a>
      </div>
   </main>
    
   <!--=============== FOOTER ===============-->
   <?php include("../layout/footer.php"); ?>
</body>
</html>
