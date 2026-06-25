<?php
require('../../../libs/fpdf/fpdf.php');
require("../inicio/conexion.php");

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image('../../../images/logos/Logo vertical.png', 10, 6, 30); // Ajusta la ruta y tamaño del logo
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Título
        $this->Cell(0, 10, utf8_decode('Lista de Clientes'), 0, 1, 'C');
        $this->Ln(10);

        // Encabezados de la tabla
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 220, 255); // Color de fondo para el encabezado
        $this->Cell(8, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(18, 10, 'DNI', 1, 0, 'C', true);  // Campo DNI
        $this->Cell(20, 10, 'Usuario', 1, 0, 'C', true);  // Campo Usuario
        $this->Cell(20, 10, utf8_decode('Nombres'), 1, 0, 'C', true);
        $this->Cell(25, 10, utf8_decode('Apellidos'), 1, 0, 'C', true);
        $this->Cell(30, 10, utf8_decode('Correo'), 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('Teléfono'), 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('Género'), 1, 0, 'C', true);  // Campo Género
        $this->Cell(30, 10, utf8_decode('Dirección'), 1, 0, 'C', true);  // Campo Dirección
        $this->Ln(10); // Salto de línea después de los encabezados
    }

    // Pie de página
    function Footer()
    {
        // Posición a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Ajustar una fila con alturas dinámicas
    function Row($data, $widths)
    {
        // Calcular la altura de la fila
        $maxHeight = 0;
        $heights = []; // Para almacenar las alturas de cada celda

        // Para cada columna, calculamos el número de líneas necesarias
        for ($i = 0; $i < count($data); $i++) {
            $heights[$i] = $this->NbLines($widths[$i], $data[$i]);
            $maxHeight = max($maxHeight, $heights[$i]);
        }
        
        // Altura de la fila
        $rowHeight = 6 * $maxHeight;

        // Salto de página si es necesario
        $this->CheckPageBreak($rowHeight);

        // Dibujar las celdas
        for ($i = 0; $i < count($data); $i++) {
            $w = $widths[$i];
            $x = $this->GetX();
            $y = $this->GetY();

            // Dibujar la celda y escribir el contenido con MultiCell
            $this->Rect($x, $y, $w, $rowHeight);
            $this->MultiCell($w, 6, utf8_decode($data[$i]), 0, 'C');
            $this->SetXY($x + $w, $y);
        }

        // Mover la posición actual después de la fila
        $this->Ln($rowHeight);
    }

    function NbLines($w, $txt)
    {
        // Número de líneas requeridas para un texto
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }
}

// Obtener datos de la base de datos
$consulta = "SELECT u.id_usuario, u.dni, u.usuario, u.nombres, u.apellidos, u.correo, u.telefono, u.direccion, u.genero
             FROM usuarios u WHERE u.id_rol = 5";
$resultado = $conexion->query($consulta);

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Anchura de cada columna
$widths = [8, 18, 20, 20, 25, 30, 20, 20, 30];

// Agregar datos al PDF
while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
    $data = [
        $row['id_usuario'],
        $row['dni'],
        $row['usuario'],
        $row['nombres'],
        $row['apellidos'],
        $row['correo'],
        $row['telefono'] ?? 'N/A',
        $row['genero'],
        $row['direccion']
    ];
    $pdf->Row($data, $widths);
}

// Guardar y/o mostrar el PDF
$nombreArchivo = "Lista_Clientes_" . date('Y-m-d_H-i-s') . ".pdf";
$pdf->Output('I', $nombreArchivo); // 'I' para abrir en navegador, 'D' para descargar
?>

