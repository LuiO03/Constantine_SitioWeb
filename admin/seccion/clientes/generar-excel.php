<?php
require '../../../libs/vendor/autoload.php'; // Carga la librería PhpSpreadsheet
include("../inicio/conexion.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Consulta SQL para obtener los datos de los clientes
$consulta = "SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, u.telefono, u.estado FROM usuarios u WHERE u.id_rol = 5";

try {
    // Preparar la consulta
    $stmt = $conexion->prepare($consulta);
    $stmt->execute();
    
    // Crear una instancia de la hoja de cálculo
    $spreadsheet = new Spreadsheet();
    
    // Seleccionar la hoja activa
    $sheet = $spreadsheet->getActiveSheet();
    
    // Combinar celdas para el título
    $sheet->mergeCells('A1:F1');
    
    // Establecer el valor de la celda combinada
    $sheet->setCellValue('A1', 'REPORTE DE CLIENTES');
    
    // Configurar el ancho de las columnas
    $sheet->getColumnDimension('A')->setWidth(10); // Ancho de la columna A
    $sheet->getColumnDimension('B')->setWidth(20); // Ancho de la columna B
    $sheet->getColumnDimension('C')->setWidth(20); // Ancho de la columna C
    $sheet->getColumnDimension('D')->setWidth(30); // Ancho de la columna D
    $sheet->getColumnDimension('E')->setWidth(15); // Ancho de la columna E
    $sheet->getColumnDimension('F')->setWidth(15); // Ancho de la columna F
    
    // Configurar el encabezado de la hoja de cálculo
    $sheet->setCellValue('A2', 'ID');
    $sheet->setCellValue('B2', 'NOMBRES');
    $sheet->setCellValue('C2', 'APELLIDOS');
    $sheet->setCellValue('D2', 'CORREO');
    $sheet->setCellValue('E2', 'TELÉFONO');
    $sheet->setCellValue('F2', 'ESTADO');
    
    // Establecer el formato de las celdas del encabezado
    $headerStyle = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];
    $sheet->getStyle('A1:F2')->applyFromArray($headerStyle);
    
    // Llenar la hoja de cálculo con los datos de la base de datos
    $row = 3; // Comienza desde la fila 3
    while ($rowData = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $estado = $rowData['estado'] == 1 ? 'Activo' : 'Inactivo'; // Mostrar estado como 'Activo' o 'Inactivo'
    
        $sheet->setCellValue('A' . $row, $rowData['id_usuario']);
        $sheet->setCellValue('B' . $row, $rowData['nombres']);
        $sheet->setCellValue('C' . $row, $rowData['apellidos']);
        $sheet->setCellValue('D' . $row, $rowData['correo']);
        $sheet->setCellValue('E' . $row, $rowData['telefono'] ?? 'N/A'); // Si no hay teléfono, mostrar 'N/A'
        $sheet->setCellValue('F' . $row, $estado);
        $row++; // Incrementar la fila
    }
    
    // Establecer el estilo para las columnas
    $sheet->getStyle('A')->applyFromArray($headerStyle);
    $sheet->getStyle('E:F')->applyFromArray($headerStyle);
    
    // Crear el escritor de archivos Excel
    $writer = new Xlsx($spreadsheet);
    
    // Definir el nombre del archivo Excel a guardar
    date_default_timezone_set('America/Lima');
    $nombre_archivo = "Lista_Clientes_" . date('Y-m-d_H-i-s') . ".xlsx";
    
    // Enviar las cabeceras necesarias para que el navegador descargue el archivo
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombre_archivo . '"');
    header('Cache-Control: max-age=0');
    
    // Guardar el archivo en la salida de la respuesta (es decir, descargarlo)
    $writer->save('php://output');
    exit;
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}
?>
