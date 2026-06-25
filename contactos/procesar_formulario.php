<?php 
   include("../admin/seccion/inicio/conexion.php");

   // Comprobar si el formulario fue enviado
   if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Recoger los datos del formulario
      $nombres = htmlspecialchars($_POST['nombres'], ENT_QUOTES, 'UTF-8');
      $apellidos = htmlspecialchars($_POST['apellidos'], ENT_QUOTES, 'UTF-8');
      $telefono = htmlspecialchars($_POST['telefono'], ENT_QUOTES, 'UTF-8');
      $correo = htmlspecialchars($_POST['correo'], ENT_QUOTES, 'UTF-8');
      $mensaje = htmlspecialchars($_POST['mensaje'], ENT_QUOTES, 'UTF-8');
      $terminos = isset($_POST['terminos']) ? 1 : 0; // Verifica si se aceptaron los términos

      // Validar que los datos no estén vacíos
      if (empty($nombres) || empty($apellidos) || empty($telefono) || empty($correo) || empty($mensaje)) {
         echo "Por favor, complete todos los campos requeridos.";
         exit;
      }

      // Validar el formato del correo
      if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
         echo "El correo electrónico no es válido.";
         exit;
      }

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
         header("Location: index.php"); // Redirige a una página de agradecimiento
         exit; // Asegúrate de terminar el script
      } else {
         echo "Error al enviar el formulario: " . implode(", ", $stmt->errorInfo());
      }
   }
?>
