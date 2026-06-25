<?php 
   include("../../admin/seccion/inicio/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Libro de Reclamaciones</title>
   <link rel="icon" href="../../images/logos/logo_solo_white.png" type="image/png">
   <link rel="stylesheet" href="../../style/nav.css">
   <link rel="stylesheet" href="../../style/base.css">
   <link rel="stylesheet" href="../../style/footer.css">
   <link rel="stylesheet" href="libro.css">
   <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
   
   <!--=============== HEADER ===============-->
   <?php include("../../layout/header.php"); ?>

   <main>
      <section class="titulo_contenido">
         <div class="titulo">
            <div class="enfasis_banner">LIBRO DE <span class="text_red">RECLAMACIONES</span></div>
         </div>
      </section>
   
      <section class="libro_reclamaciones">
         <div class="contenedor_libro sub_bloque">
            <p class="mensaje_importante">
               <i class="ri-information-line"></i>
               En Constantine, nos esforzamos por brindar un servicio excepcional a todos nuestros clientes. Si has tenido alguna
               experiencia que no cumplió con tus expectativas, por favor háznoslo saber. Tu opinión nos ayuda a mejorar
               continuamente nuestros productos y servicios.
            </p>
     
            <form class="form_reclamaciones" action="#" method="POST">
               <!-- Fecha -->
               <div class="form_grupo fecha">
                  <label><i class="ri-calendar-line"></i> Fecha:</label>
                  <div class="fecha-campos">
                        <input type="number" name="dia" placeholder="Día" required min="1" max="31">
                        <input type="number" name="mes" placeholder="Mes" required min="1" max="12">
                        <input type="number" name="anio" placeholder="Año" required min="1900" max="2100">
                  </div>
               </div>
   
               <!-- Local donde se realizó la compra/servicio -->
               <div class="form_grupo">
                  <label for="local-compra"><i class="ri-store-line"></i> Local donde se realizó la compra o servicio:</label>
                  
                  <select id="local-compra" name="local-compra" required>
                     <option value="">Selecciona un local</option>
                     <option value="Local 1">Local 1 - Calle Principal</option>
                     <option value="Local 2">Local 2 - Av. Secundaria</option>
                     <option value="Local 3">Local 3 - Centro Comercial</option>
                  </select>
               </div>
   
               <!-- Identificación del Consumidor Reclamante -->
               <h3 class="titulo_lista"><i class="ri-user-line"></i> 1. Identificación del Consumidor Reclamante</h3>
   
               <div class="form_grupo">
                  <label for="nombre-consumidor">Nombre:</label>
                  <input type="text" id="nombre-consumidor" name="nombre-consumidor" required placeholder="Tu nombre completo" maxlength="100">
               </div>
   
               <div class="form_grupo">
                  <label for="domicilio-consumidor">Domicilio:</label>
                  <input type="text" id="domicilio-consumidor" name="domicilio-consumidor" required placeholder="Tu domicilio" maxlength="150">
               </div>
   
               <div class="form_grupo">
                  <label for="dni-consumidor">DNI:</label>
                  <input type="text" id="dni-consumidor" name="dni-consumidor" required placeholder="Tu DNI" pattern="\d{8}" title="Debe contener 8 dígitos">
               </div>
   
               <div class="form_grupo">
                  <label for="telefono-email">Teléfono / Email:</label>
                  <input type="email" id="telefono-email" name="telefono-email" required placeholder="Tu teléfono o email" maxlength="50">
               </div>
   
               <div class="form_grupo">
                  <label for="padre-madre">Padre o Madre (si es menor de edad):</label>
                  <input type="text" id="padre-madre" name="padre-madre" placeholder="Nombre del padre o madre" maxlength="100">
               </div>
   
               <!-- Identificación del Bien Contratado -->
               <h3 class="titulo_lista">
                  <i class="ri-shopping-bag-line"></i> 2. Identificación del Bien Contratado
               </h3>
   
               <div class="form_grupo">
                  <label>¿Es un producto o servicio?</label>
   
                  <div class="radio_group">
                     <label class="radio_label">
                        <input type="radio" name="bien-contratado" value="producto" required>
                        <div>Producto</div>
                     </label>
                     <label class="radio_label">
                        <input type="radio" name="bien-contratado" value="servicio">
                        <div>Servicio</div>
                     </label>
                  </div>
               </div>
   
               <div class="form_grupo">
                  <label for="monto-reclamado">Monto Reclamado:</label>
                  <input type="number" id="monto-reclamado" name="monto-reclamado" required placeholder="Monto en S/" step="0.01" min="0">
               </div>
   
               <div class="form_grupo">
                  <label for="descripcion-bien">Descripción:</label>
                  <textarea id="descripcion-bien" name="descripcion-bien" required placeholder="Describe el bien contratado" maxlength="500"></textarea>
               </div>
   
               <!-- Detalle de la Reclamación -->
               <h3 class="titulo_lista"><i class="ri-file-list-line">
                  </i> 3. Detalle de la Reclamación y Pedido del Consumidor
               </h3>
   
               <div class="form_grupo">
                  <label>¿Es un reclamo o queja?</label>
   
                  <div class="radio_group">
                     <label class="radio_label">
                        <input type="radio" name="tipo-reclamo" value="reclamo" required>
                        <div>Reclamo</div>
                     </label>
                     <label class="radio_label">
                        <input type="radio" name="tipo-reclamo" value="queja">
                        <div>Queja</div>
                     </label>
                  </div>
   
               </div>
   
               <div class="form_grupo">
                  <label for="detalle-reclamacion">Detalle:</label>
                  <textarea id="detalle-reclamacion" name="detalle-reclamacion" required placeholder="Describe el reclamo o queja" maxlength="500"></textarea>
               </div>
   
               <div class="form_grupo">
                  <label for="firma-consumidor">Firma del Consumidor:</label>
                  <input type="text" id="firma-consumidor" name="firma-consumidor" required placeholder="Tu firma">
               </div>
   
               <!-- Observaciones del Proveedor -->
               <h3 class="titulo_lista"><i class="ri-notification-line"></i> 4. Observaciones y Acciones Adoptadas por el Proveedor</h3>
               <div class="form_grupo fecha">
                  <label>Fecha de Comunicación de Respuesta:</label>
                  <div class="fecha-campos">
                        <input type="number" name="dia-respuesta" placeholder="Día" required min="1" max="31">
                        <input type="number" name="mes-respuesta" placeholder="Mes" required min="1" max="12">
                        <input type="number" name="anio-respuesta" placeholder="Año" required min="1900" max="2100">
                  </div>
               </div>
   
               <div class="form_grupo">
                  <label for="firma-proveedor">Firma del Proveedor:</label>
                  <input type="text" id="firma-proveedor" name="firma-proveedor" required placeholder="Firma del proveedor">
               </div>
   
               <!-- Botón de envío -->
               <button type="submit" class="btn_enviar">
                  <i class="ri-send-plane-line"></i> Enviar Reclamación
               </button>
            </form>
         </div>
      </section>
   </main>

   <!--=============== FOOTER ===============-->
   <?php include("../../layout/footer.php"); ?>
      

