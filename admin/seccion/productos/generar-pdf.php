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
        $this->Cell(0, 10, utf8_decode('Lista de Productos'), 0, 1, 'C');
        $this->Ln(10);

        // Encabezados de la tabla
        $this->SetFont('Arial', 'B', 11);
        $this->SetFillColor(200, 220, 255); // Color de fondo para el encabezado
        $this->Cell(13, 10, 'ID', 1, 0, 'C', true);
        $this->Cell (27, 10, utf8_decode('Código'), 1, 0, 'C', true);  
        $this->Cell(48, 10, utf8_decode('Nombre'), 1, 0, 'C', true);
        $this->Cell(28, 10, utf8_decode('Categoría'), 1, 0, 'C', true);
        $this->Cell(34, 10, utf8_decode('SubCategoría'), 1, 0, 'C', true);
        $this->Cell(24, 10, utf8_decode('Stock Total'), 1, 0, 'C', true);
        $this->Cell(20, 10, utf8_decode('Precio'), 1, 0, 'C', true);
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

// Crear el objeto PDF
$pdf = new PDF();
$pdf->AddPage();

// Establecer conexión con la base de datos y obtener los productos
$sentencia = $conexion->prepare("SELECT p.*, c.nombre_categoria, pub.nombre_publico FROM productos p 
                                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                                LEFT JOIN publico pub ON p.id_publico = pub.id_publico");
$sentencia->execute();
$productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

// Mostrar los datos en la tabla del PDF
$pdf->SetFont('Arial', '', 10);

// Ancho de las columnas
$widths = [13, 27, 48, 28, 34, 24, 20];  // Ancho de las celdas

foreach ($productos as $producto) {
    $data = [
        $producto['id_producto'],
        $producto['codigo_producto'],
        $producto['nombre_producto'],
        $producto['nombre_categoria'],
        $producto['nombre_publico'],
        $producto['stock_total'],
        'S/ ' . $producto['precio_venta']
    ];

    $pdf->Row($data, $widths);
}

// Salida del PDF
$pdf->Output();
?>












