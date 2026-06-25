<?php 
	date_default_timezone_set('America/Lima');
	function fechaC(){
		$mes = array(
			"",
			"Enero", 
			"Febrero", 
			"Marzo", 
			"Abril", 
			"Mayo", 
			"Junio", 
			"Julio", 
			"Agosto", 
			"Septiembre", 
			"Octubre", 
			"Noviembre", 
			"Diciembre"
		);
		
		$dia = array("domingo", "lunes",  "martes", "miércoles", "jueves", "viernes",  "sábado");

		return "Hoy es " . $dia[date("w")]."," . " " . date("d") . " de " . $mes[date('n')] . " de " . date('Y');
	}
 ?>
 <?php echo fechaC(); ?>