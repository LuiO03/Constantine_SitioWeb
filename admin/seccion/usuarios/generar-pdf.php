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
        $this->Cell(0, 10, utf8_decode('Lista de Usuarios'), 0, 1, 'C');
        $this->Ln(10);

        // Encabezados de la tabla
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 220, 255); // Color de fondo para el encabezado
        $this->Cell(18, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(33, 10, utf8_decode('Nombres'), 1, 0, 'C', true);
        $this->Cell(33, 10, utf8_decode('Apellidos'), 1, 0, 'C', true);
        $this->Cell(40, 10, utf8_decode('Correo'), 1, 0, 'C', true);
        $this->Cell(30, 10, utf8_decode('Teléfono'), 1, 0, 'C', true);
        $this->Cell(37, 10, utf8_decode('Rol'), 1, 1, 'C', true);
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
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($widths[$i], $data[$i]));
        }
        $h = 6 * $nb;

        // Salto de página si es necesario
        $this->CheckPageBreak($h);

        // Dibujar las celdas
        for ($i = 0; $i < count($data); $i++) {
            $w = $widths[$i];
            $x = $this->GetX();
            $y = $this->GetY();

            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 6, utf8_decode($data[$i]), 0, 'C');
            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
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
$consulta = "SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, u.telefono, r.rol_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.id_rol != 5";
$resultado = $conexion->query($consulta);

// Crear el objeto PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Anchura de cada columna
$widths = [18, 33, 33, 40, 30, 37];

// Agregar los datos al PDF
while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
    $data = [
        $row['id_usuario'],
        $row['nombres'],
        $row['apellidos'],
        $row['correo'],
        $row['telefono'] ?? 'N/A',
        $row['rol_nombre'] ?? 'N/A'
    ];
    $pdf->Row($data, $widths);
}

// Guardar y/o mostrar el PDF
$nombreArchivo = "Lista_Usuarios_" . date('Y-m-d_H-i-s') . ".pdf";
$pdf->Output('I', $nombreArchivo); // 'I' para abrir en navegador, 'D' para descargar
?>







