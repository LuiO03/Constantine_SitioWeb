<?php 
   include("admin/seccion/inicio/conexion.php");

   $sentencia=$conexion->prepare("SELECT * FROM categorias ORDER BY id_categoria DESC");
   $sentencia->execute();
   $lista_categorias= $sentencia->fetchAll(PDO::FETCH_ASSOC);

   $sentencia=$conexion->prepare("SELECT * FROM banners WHERE pagina = 'inicio' AND estado = 1 ORDER BY id_banner asc;");
   $sentencia->execute();
   $lista_banners= $sentencia->fetchAll(PDO::FETCH_ASSOC);
   
   $query = "SELECT p.*, c.nombre_categoria, pub.nombre_publico
      FROM productos p
      JOIN categorias c ON p.id_categoria = c.id_categoria
      JOIN publico pub ON p.id_publico = pub.id_publico"; // Unir con la tabla publico
   $productos = $conexion->query($query);

   // Separar los productos por público
   $productos_damas = [];
   $productos_caballeros = [];
   $productos_ninos = [];
   $productos_ninas = [];

   foreach ($productos as $producto) {
      if ($producto['id_publico'] == 1) {
         $productos_caballeros[] = $producto;
      } elseif ($producto['id_publico'] == 2) {
         $productos_damas[] = $producto;
      } elseif ($producto['id_publico'] == 3) {
         $productos_ninos[] = $producto;
      } elseif ($producto['id_publico'] == 4) {
         $productos_ninas[] = $producto;
      }
   }

   // Tab 1: Lo más nuevo (ordenado por id_producto DESC)
   $query_nuevo = "SELECT p.*, c.nombre_categoria, pub.nombre_publico
   FROM productos p
   JOIN categorias c ON p.id_categoria = c.id_categoria
   JOIN publico pub ON p.id_publico = pub.id_publico
   ORDER BY p.id_producto DESC
   LIMIT 8";
   $productos_nuevo = $conexion->query($query_nuevo)->fetchAll(PDO::FETCH_ASSOC);

   // Tab 2: Lo más vendido (suponiendo que tienes un campo `ventas` en la tabla)
   $query_vendido = "SELECT p.*, c.nombre_categoria, pub.nombre_publico
   FROM productos p
   JOIN categorias c ON p.id_categoria = c.id_categoria
   JOIN publico pub ON p.id_publico = pub.id_publico
   ORDER BY p.precio_venta DESC
   LIMIT 8";
   $productos_vendido = $conexion->query($query_vendido)->fetchAll(PDO::FETCH_ASSOC);

   // Tab 3: Lo más barato (ordenado por precio_venta ASC)
   $query_barato = "SELECT p.*, c.nombre_categoria, pub.nombre_publico
   FROM productos p
   JOIN categorias c ON p.id_categoria = c.id_categoria
   JOIN publico pub ON p.id_publico = pub.id_publico
   ORDER BY p.precio_venta ASC
   LIMIT 8";
   $productos_barato = $conexion->query($query_barato)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
   <html lang="es">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">

      <link rel="icon" href="images/logos/logo_solo_white.png" type="image/png">
      <!--=============== CSS ===============-->
      <link rel="stylesheet" href="style/nav.css">
      <link rel="stylesheet" href="style/base.css">
      <link rel="stylesheet" href="style/footer.css">
      <link rel="stylesheet" href="style/css.css">
      <!--=============== REMIXICONS ===============-->
      <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

      <!-- jQuery (necesario para Owl Carousel) -->
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

      <!-- CSS personalizado -->
      <link rel="stylesheet" href="css/productos.css">

      <!-- CSS de Owl Carousel -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

      <!-- Incluye animate.css en tu proyecto -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

      <!-- JavaScript de Owl Carousel -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
      
      <title>Constantine - Tienda de Ropa</title>
   </head>
   <body>

      <!--=============== HEADER ===============-->
      <?php include("layout/header.php"); ?>

      <main>
         <!--=============== BANNER ===============-->
         <section class="banner">
            <div class="carousel_banner">

               <?php foreach($lista_banners as $banner){  
                  $rutaImagen = 'images/banners/' . $banner['imagen'];

                  if (!file_exists($rutaImagen) || empty($banner['imagen'])) {
                      $rutaImagen = 'images/banners/banner_default.jpg';
                  }
               ?>
                  <div class="slide" style="background-image: url('<?php echo $rutaImagen; ?>');">
                     <div class="caption">
                        <div class="titulo_banner"><?php echo $banner['titulo'];?></div>
                        <div class="enfasis_banner"><?php echo $banner['enfasis'];?></div>
                        <div class="descripcion_banner"><?php echo $banner['descripcion'];?></div>
                        <div class="botones_banner">
                           <a href="productos/?publico=" class="boton_1">Explorar</a>
                           <a href="#ofertas" class="boton_2"">Comprar Ahora</a>
                        </div>
                     </div>
                  </div>
               <?php  } ?>
   
            </div>
            <!-- Botones de navegación -->
            <button class="btn_banner_prev">
               <i class="ri-arrow-left-s-line"></i>
            </button>
            <button class="btn_banner_next">
               <i class="ri-arrow-right-s-line"></i>
            </button>
   
            <div class="indicators">
               <span class="dot"></span>
               <span class="dot"></span>
               <span class="dot"></span>
            </div>
         </section>
   
         <!--=============== MENÚ TABS ===============-->
         <section class="menutab__container bloque_1">
            <div class="titulo_cuerpo">NUEVAS <span class="text_red">COLECCIONES</span></div>
            <div class="subtitulo_cuerpo">
               Descubre las últimas tendencias de la temporada y redefine tu estilo con nuestras prendas exclusivas.
            </div>
            <div class="tab_contenido sub_bloque">
               <input type="radio" name="group1" id="radio1" checked>
               <input type="radio" name="group1" id="radio2">
               <input type="radio" name="group1" id="radio3">

               <label id="lbl1" for="radio1">
                     <h3>LO MÁS NUEVO</h3>
               </label>
               <label id="lbl2" for="radio2">
                     <h3>LO MÁS DESTACADO</h3>
               </label>
               <label id="lbl3" for="radio3">
                     <h3>LO MÁS BARATO</h3>
               </label>

               <!-- Tab 1: Lo más nuevo -->
               <article class="tab1 productos">
                  <?php if (count($productos_nuevo) > 0): ?>
                     <?php foreach ($productos_nuevo as $producto): ?>
                        <div class="producto">
                              <div class="imagen_contendedor">
                                 <img src="images/productos/<?php echo htmlspecialchars($producto['nombre_publico']); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                 <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                    <i class="ri-eye-fill"></i>
                                    <span>VER</span>
                                 </a>
                              </div>
                              <div class="producto_info">
                                 <h3>Constantine <?php echo htmlspecialchars($producto['nombre_publico']); ?></h3>
                                 <p><?php echo htmlspecialchars($producto['nombre_producto']); ?></p>
                                 <div class="precio_info">
                                    <span class="precio">S/.<?php echo number_format($producto['precio_venta'], 2); ?></span>
                                    <div class="producto_botones">
                                          
                                          <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>"><i class="ri-heart-line"></i></a>
                                    </div>
                                 </div>
                              </div>
                        </div>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p class="no-productos">No hay productos disponibles.</p>
                  <?php endif; ?>
               </article>

               <!-- Tab 2: Lo más vendido -->
               <article class="tab2 productos">
                  <?php if (count($productos_vendido) > 0): ?>
                     <?php foreach ($productos_vendido as $producto): ?>
                        <div class="producto">
                              <div class="imagen_contendedor">
                                 <img src="images/productos/<?php echo htmlspecialchars($producto['nombre_publico']); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                 <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                    <i class="ri-eye-fill"></i>
                                    <span>VER</span>
                                 </a>
                              </div>
                              <div class="producto_info">
                                 <h3>Constantine <?php echo htmlspecialchars($producto['nombre_publico']); ?></h3>
                                 <p><?php echo htmlspecialchars($producto['nombre_producto']); ?></p>
                                 <div class="precio_info">
                                    <span class="precio">S/.<?php echo number_format($producto['precio_venta'], 2); ?></span>
                                    <div class="producto_botones">
                                          
                                          <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>"><i class="ri-heart-line"></i></a>
                                    </div>
                                 </div>
                              </div>
                        </div>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p class="no-productos">No hay productos disponibles.</p>
                  <?php endif; ?>
               </article>

               <!-- Tab 3: Lo más barato -->
               <article class="tab3 productos">
               <?php if (count($productos_barato) > 0): ?>
                  <?php foreach ($productos_barato as $producto): ?>
                        <div class="producto">
                              <div class="imagen_contendedor">
                                 <img src="images/productos/<?php echo htmlspecialchars($producto['nombre_publico']); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                 <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                    <i class="ri-eye-fill"></i>
                                    <span>VER</span>
                                 </a>
                              </div>
                              <div class="producto_info">
                                 <h3>Constantine <?php echo htmlspecialchars($producto['nombre_publico']); ?></h3>
                                 <p><?php echo htmlspecialchars($producto['nombre_producto']); ?></p>
                                 <div class="precio_info">
                                    <span class="precio">S/.<?php echo number_format($producto['precio_venta'], 2); ?></span>
                                    <div class="producto_botones">
                                          
                                          <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>"><i class="ri-heart-line"></i></a>
                                    </div>
                                 </div>
                              </div>
                        </div>
                     <?php endforeach; ?>
                  <?php else: ?>
                     <p class="no-productos">No hay productos disponibles.</p>
                  <?php endif; ?>
               </article>

               <div class="boton_contenedor">
                  <a href="productos/?publico=" class="boton_general">
                     <i class="ri-shopping-bag-2-fill"></i>
                     <span>VER MÁS PRODUCTOS</span>
                  </a>
               </div>
            </div>
         </section>

         <!--=============== CATEGORÍAS ===============-->
         <section class="categorias bloque_1">
            <div class="titulo_cuerpo">EXPLORA POR <span class="text_red">CATEGORÍAS</span></div>
            <div class="subtitulo_cuerpo">
               Descubre nuestra amplia selección de productos organizados por categorías para encontrar lo que más se ajusta a tu estilo.
            </div>
      
            <div class="categorias_grid sub_bloque">

               <?php  foreach($lista_categorias as $categoria){  ?>
                  <a href="productos/?publico=&categoria=<?php echo $categoria['nombre_categoria'];?>" class="categoria" style="background-image: url('images/categorias/<?php echo $categoria['imagen'];?>');">
                     <div class="contenido_tarjeta">
                        <div class="titulo_lista"><?php echo $categoria['nombre_categoria'];?></div>
                     </div>
                  </a>
               <?php  } ?>
               
            </div>
         </section>
         
         <!--=============== CABALLEROS ===============-->
         <section class="carousel_productos bloque_completo1" id="caballeros">
            <div class="titulo_cuerpo">PARA <span class="text_red">CABALLEROS</span></div>
            <div class="subtitulo_cuerpo">
               Ropa que define tu estilo. Las mejores opciones para caballeros están aquí.
            </div>
   
            <div class="carousel_container sub_bloque2">
               <button class="publico_boton_atras" id="button-prev-caballeros" data-button="button-prev-caballeros">
                  <i class="ri-arrow-left-s-line"></i>
               </button>
               <div class="wrapper">
                  <div class="carousel owl-carousel">
                     <?php foreach ($productos_caballeros as $producto) { ?>
                        <div class="producto">
                           <div class="imagen_contendedor">
                              <img src="images/productos/<?php echo strtolower(htmlspecialchars($producto['nombre_publico'])); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                              <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                 <i class="ri-eye-fill"></i>
                                 <span>VER</span>
                              </a>
                           </div>
                           <div class="producto_info">
                              <p>
                                    <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                              </p>
                              <div class="precio_info">
                                    <span class="precio">
                                       S/.<?php echo number_format($producto['precio_venta'], 2); ?>
                                    </span>
                                    <div class="producto_botones">
                                       
                                       <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>">
                                          <i class="ri-heart-line"></i>
                                       </a>
                                    </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
               </div>
               <button class="publico_boton_siguiente" id="button-next-caballeros" data-button="button-next-caballeros">
                     <i class="ri-arrow-right-s-line"></i>
               </button>
            </div>
   
            <div class="boton_contenedor">
               <a href="productos/?publico=1" class="boton_general">
                  <i class="ri-men-line"></i>
                  <span>VER MÁS ROPA PARA CABALLEROS</span> 
               </a>
         </div>
         </section>

         <!--=============== DAMAS ===============-->
         <section class="carousel_productos bloque_completo1" id="damas">
            <div class="titulo_cuerpo">PARA <span class="text_red">DAMAS</span></div>
            <div class="subtitulo_cuerpo">
               Moda y diseñados para resaltar tu esencia única. Encuentra las últimas tendencias.
            </div>
   
            <div class="carousel_container sub_bloque2">
               <button class="publico_boton_atras" id="button-prev-damas" data-button="button-prev-damas">
                  <i class="ri-arrow-left-s-line"></i>
               </button>
               <div class="wrapper">
                  <div class="carousel owl-carousel">
                     <?php foreach ($productos_damas as $producto) { ?>
                        <div class="producto">
                           <div class="imagen_contendedor">
                              <img src="images/productos/<?php echo strtolower(htmlspecialchars($producto['nombre_publico'])); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                              <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                 <i class="ri-eye-fill"></i>
                                 <span>VER</span>
                              </a>
                           </div>
                           <div class="producto_info">
                              <p>
                                 <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                              </p>
                              <div class="precio_info">
                                    <span class="precio">
                                       S/.<?php echo number_format($producto['precio_venta'], 2); ?>
                                    </span>
                                    <div class="producto_botones">
                                       
                                       <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>">
                                          <i class="ri-heart-line"></i>
                                       </a>
                                    </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
               </div>
               <button class="publico_boton_siguiente" id="button-next-damas" data-button="button-next-damas">
                     <i class="ri-arrow-right-s-line"></i>
               </button>
            </div>
   
            <div class="boton_contenedor">
               <a href="productos/?publico=2" class="boton_general">
                  <i class="ri-women-line"></i>
                  <span>
                     VER MÁS ROPA PARA DAMAS
                  </span>
               </a>
            </div>
         </section>
   
         <!--=============== NIÑOS ===============-->
         <section class="carousel_productos bloque_completo1" id="ninos">
            <div class="titulo_cuerpo">PARA <span class="text_red">NIÑOS</span></div>
            <div class="subtitulo_cuerpo">
               Ropa divertida y cómoda para los más pequeños. ¡Explora nuestra colección para niños!
            </div>
   
            <div class="carousel_container sub_bloque2">
               <button class="publico_boton_atras" id="button-prev-ninos" data-button="button-prev-ninos">
                  <i class="ri-arrow-left-s-line"></i>
               </button>
               <div class="wrapper">
                  <div class="carousel owl-carousel">
                     <?php foreach ($productos_ninos as $producto) { ?>
                        <div class="producto">
                           <div class="imagen_contendedor">
                           <img src="images/productos/<?php echo strtolower(htmlspecialchars($producto['nombre_publico'])); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                              <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                 <i class="ri-eye-fill"></i>
                                 <span>VER</span>
                              </a>
                           </div>
                           <div class="producto_info">
                              <p>
                                    <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                              </p>
                              <div class="precio_info">
                                    <span class="precio">
                                       S/.<?php echo number_format($producto['precio_venta'], 2); ?>
                                    </span>
                                    <div class="producto_botones">
                                       
                                       <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>">
                                          <i class="ri-heart-line"></i>
                                       </a>
                                    </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
               </div>
               <button class="publico_boton_siguiente" id="button-next-ninos" data-button="button-next-ninos">
                     <i class="ri-arrow-right-s-line"></i>
               </button>
            </div>
   
            <div class="boton_contenedor">
               <a href="productos/?publico=3" class="boton_general">
                  <i class="ri-football-fill"></i>
                  <span>VER MÁS ROPA PARA NIÑOS</span> 
               </a>
         </div>
         </section>

         <!--=============== NIÑAS ===============-->
         <section class="carousel_productos bloque_completo1" id="ninas">
            <div class="titulo_cuerpo">PARA <span class="text_red">NIÑAS</span></div>
            <div class="subtitulo_cuerpo">
               Ropa divertida y cómoda para los más pequeños. ¡Explora nuestra colección para niños!
            </div>
   
            <div class="carousel_container sub_bloque2">
               <button class="publico_boton_atras" id="button-prev-ninas" data-button="button-prev-ninas">
                  <i class="ri-arrow-left-s-line"></i>
               </button>
               <div class="wrapper">
                  <div class="carousel owl-carousel">
                     <?php foreach ($productos_ninas as $producto) { ?>
                        <div class="producto">
                           <div class="imagen_contendedor">
                           <img src="images/productos/<?php echo strtolower(htmlspecialchars($producto['nombre_publico'])); ?>/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                              <a class="ver_producto" href="productos/producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
                                 <i class="ri-eye-fill"></i>
                                 <span>VER</span>
                              </a>
                           </div>
                           <div class="producto_info">
                              <p>
                                    <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                              </p>
                              <div class="precio_info">
                                    <span class="precio">
                                       S/.<?php echo number_format($producto['precio_venta'], 2); ?>
                                    </span>
                                    <div class="producto_botones">
                                       
                                       <a class="btn_agregar" href="<?php echo $producto['id_producto']; ?>">
                                          <i class="ri-heart-line"></i>
                                       </a>
                                    </div>
                              </div>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
               </div>
               <button class="publico_boton_siguiente" id="button-next-ninas" data-button="button-next-ninas">
                     <i class="ri-arrow-right-s-line"></i>
               </button>
            </div>
   
            <div class="boton_contenedor">
               <a href="productos/?publico=4" class="boton_general">
                  <i class="ri-football-fill"></i>
                  <span>VER MÁS ROPA PARA NIÑAS</span> 
               </a>
         </div>
         </section>
         
         <!--=============== RAZONES DE COMPRA ===============-->
         <section class="razon__container bloque_1">
   
            <div class="titulo_cuerpo">¿POR QUÉ COMPRAR CON <span class="text_red">NOSOTROS?</span></div>
            
            <div class="razones sub_bloque">
               <!-- Envío Rápido -->
               <div class="razon">
                  <div class="circleborder">
                     <i class="ri-home-heart-fill"></i>
                  </div>
                  <h4>ENVÍO RÁPIDO</h4>
                  <p class="parrafo_cuerpo">Entrega rápida y segura, cumpliendo con los tiempos prometidos para mayor satisfacción del cliente.</p>
               </div>
   
               <!-- Mejor Calidad -->
               <div class="razon">
                  <div class="circleborder">
                     <i class="ri-shirt-fill"></i>
                  </div>
                  <h4>MEJOR CALIDAD</h4>
                  <p class="parrafo_cuerpo">Te garantizamos productos de la más alta calidad, cuidando cada detalle en nuestras prendas.</p>
               </div>
   
               <!-- Mejores Ofertas -->
               <div class="razon">
                  <div class="circleborder">
                     <i class="ri-money-dollar-box-fill"></i>
                  </div>
                  <h4>MEJORES OFERTAS</h4>
                  <p class="parrafo_cuerpo">Descuentos exclusivos y ofertas increíbles para que consigas más por menos.</p>
               </div>
   
               <!-- Pagos Seguros -->
               <div class="razon">
                  <div class="circleborder">
                     <i class="ri-lock-2-fill"></i>
                  </div>
                  <h4>PAGOS SEGUROS</h4>
                  <p class="parrafo_cuerpo">Tus transacciones están protegidas, brindando seguridad en cada compra.</p>
               </div>
            </div>
         </section>
      </main>

      <?php include("layout/footer.php"); ?>