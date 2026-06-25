<?php 
   include("../admin/seccion/inicio/conexion.php"); 

   $sentencia=$conexion->prepare("SELECT * FROM banners WHERE pagina = 'blog' AND estado = 1;");
   $sentencia->execute();
   $lista_banners = $sentencia->fetchAll(PDO::FETCH_ASSOC); 

   $sentencia = $conexion->prepare("SELECT * FROM blogs ");
   $sentencia->execute(); 
   $lista_blogs = $sentencia->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html> 
<html lang="es"> 
<head> 
   <meta charset="UTF-8"> 
   <meta name="viewport" content="width=device-width, initial-scale=1.0" > 
   <title>Constantine - Blog</title> 
   <link rel="icon" href="../images/logos/logo_solo_white.png" type="image/png"> 
   <link rel="stylesheet" href="../style/nav.css"> 
   <link rel="stylesheet" href="../style/base.css"> 
   <link rel="stylesheet" href="../style/footer.css"> 
   <link rel="stylesheet" href="blog.css"> 
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
      
      <!-- Sección de artículos -->
      <section class="blog__articulos bloque_1">
         <div class="contenedor">
            <?php foreach ($lista_blogs as $blog) { 
               // Dividir el contenido del blog en párrafos
               $contenido_parrafos = explode("\n", $blog['contenido']); 
               $primer_parrafo = $contenido_parrafos[0]; // Solo el primer párrafo
            ?> 
               <div class="articulo">
                  <div class="articulo_img">
                     <img src="img/<?php echo $blog['imagen']; ?>" alt="<?php echo $blog['titulo_blog']; ?>"> 
                  </div>
                  <div class="articulo_info">
                     <h2 class="articulo_titulo"><?php echo $blog['titulo_blog']; ?></h2>
                     <p class="categoria"><i class="ri-<?php echo $blog['categoria']; ?>-line"></i> <?php echo $blog['categoria']; ?></p>
                     
                     <!-- Mostrar solo el primer párrafo -->
                     <p class="articulo_resumen"><?php echo nl2br($primer_parrafo); ?></p>

                     <!-- El botón "Leer más" funciona sin cambios -->
                     <a href="blog_detalle.php?id=<?php echo $blog['id_blog']; ?>" class="contenedor_leermas">
                        <button class="boton_leermas">
                           <span class="boton_icono" aria-hidden="true">
                              <span class="icon arrow"></span>
                           </span>
                           <div class="boton_texto">Leer Más</div>
                        </button>
                     </a>
                  </div>
               </div>
            <?php } ?> 
         </div>

         <!--=============== ASIDE ===============-->
         <aside class="sidebar">
            <div class="sidebar__widget">
               <div class="titulo_lista">CATEGORÍAS</div>
               <ul class="widget_list">
                  <li><a href="#">Moda</a></li>
                  <li><a href="#">Tecnología</a></li>
                  <li><a href="#">Estilo</a></li>
                  <li><a href="#">Sostenibilidad</a></li>
                  <li><a href="#">Emprendimiento</a></li>
                  <li><a href="#">Innovación</a></li>
               </ul>
            </div>

            <div class="sidebar__widget">
               <div class="titulo_lista">ENTRADAS RECIENTES</div>
               <ul class="widget_list">
                  <li><a href="#">Las Tendencias de Moda en 2024</a></li>
                  <li><a href="#">La Tecnología y su Impacto en la Industria de la Ropa</a></li>
                  <li><a href="#">Consejos de Estilo para el Trabajo</a></li>
                  <li><a href="#">El Futuro de la Moda Sostenible</a></li>
                  <li><a href="#">Cómo Iniciar tu Propia Marca de Ropa</a></li>
               </ul>
            </div>
         </aside>
      </section>
   </main>
    
   <!--=============== FOOTER ===============--> 
   <?php include("../layout/footer.php"); ?>

</body>
</html>
