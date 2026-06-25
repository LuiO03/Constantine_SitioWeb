<!--=============== FORMULARIO DE CONTACTO ===============-->
<section class="contacto__formulario bloque_completo1">
   <div class="formulario__contenedor">
      <div class="titulo_cuerpo">
         <span class="text_white">RELLENE NUESTRO</span> <span class="text_red">FORMULARIO</span>
      </div>
      <div class="subtitulo_cuerpo">
         <span class="text_white">
            Envíenos sus dudas y comentarios, nos pondremos en contacto con usted.
         </span>
      </div>

      <form class="form-register" action="procesar_formulario.php" method="POST">
         <div class="input-group">
            <!-- Los campos de nombre y apellido ya tienen validación de formato con pattern -->
            <input class="controls" type="text" name="nombres" id="nombres" placeholder="Ingrese su Nombre" 
                   required minlength="2" maxlength="25" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras y espacios, máximo 25 caracteres">
            <input class="controls" type="text" name="apellidos" id="apellidos" placeholder="Ingrese su Apellido" 
                   required minlength="2" maxlength="25" pattern="[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+" title="Solo letras y espacios, máximo 25 caracteres">
         </div>

         <div class="input-group">
            <!-- El campo de teléfono también tiene validación de formato -->
            <input class="controls" type="tel" name="telefono" id="telefono" placeholder="Ingrese su Teléfono" 
                   required minlength="9" maxlength="15" pattern="[0-9]{7,15}" title="Solo números, entre 9 y 15 dígitos"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <input class="controls" type="email" name="correo" id="correo" placeholder="Ingrese su Correo" 
                   required maxlength="50" title="Ingrese un correo válido">
         </div>

         <!-- Campo de mensaje con validación de longitud -->
         <textarea class="controls" name="mensaje" id="mensaje" placeholder="Ingrese su Mensaje" 
                   required minlength="10" maxlength="500" title="El mensaje debe tener entre 10 y 500 caracteres"></textarea>

         <div class="checkbox_grupo">
            <!-- Verificación de los términos y condiciones -->
            <input type="checkbox" name="terminos" id="terminos" required>
            <label for="terminos">Estoy de acuerdo con <a href="#">Términos y Condiciones</a></label>
         </div>

         <button class="botons" type="submit">
            Enviar Mensaje <i class="ri-send-plane-fill"></i>
         </button>
      </form>
   </div>
</section>

<script>
   // Validación en tiempo real para los campos de nombre y apellido
   document.getElementById('nombres').addEventListener('input', function (e) {
      e.target.value = e.target.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
   });

   document.getElementById('apellidos').addEventListener('input', function (e) {
      e.target.value = e.target.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúñÑ\s]/g, '');
   });
</script>
