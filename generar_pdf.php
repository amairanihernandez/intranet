<?php
require('fpdf/fpdf.php');

// Obtener el ID de la orden desde la URL
$orden_id = $_GET['id'];

// Directorio para almacenar los PDFs
$directorio_pdfs = 'pdfs';

// Verificar si el directorio existe, si no, crearlo
if (!file_exists($directorio_pdfs) && !is_dir($directorio_pdfs)) {
    mkdir($directorio_pdfs);
}

// Crear una instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Definir posición y tamaño del recuadro
$pdf->SetDrawColor(0, 0, 0); // Color del borde
$pdf->SetFillColor(200, 220, 255); // Color del fondo
$pdf->SetLineWidth(0.5); // Ancho del borde
$pdf->Rect(10, 10, 190, 30, 'DF'); // Dibuja el recuadro

// Logo
$pdf->Image('logo/logo.png', 15, 12, 30); // Reemplaza con la ruta de tu logo

// Encabezado
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetXY(50, 15);
$pdf->Cell(0, 10, 'Caja Cerro de la Silla SC de AP de RL de CV', 0, 1, 'L');
$pdf->SetXY(70, 25);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Cooperativa de Ahorro y Prestamo', 0, 1, 'L');

// Definir posición y tamaño del recuadro inferior
$pdf->SetDrawColor(0, 0, 0); // Color del borde
$pdf->SetFillColor(200, 220, 255); // Color del fondo
$pdf->SetLineWidth(0.5); // Ancho del borde
$pdf->Rect(10, 45, 190, 15, 'DF'); // Dibuja el recuadro inferior

// Texto en el recuadro inferior
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetXY(60, 48);
$pdf->Cell(0, 10, 'Orden de Compra o Requisicion', 0, 1, 'L');

// Consultar detalles de productos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "orden_compra1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar información adicional de la orden
$sql_info_orden = "SELECT solicitante_id, proveedor, sucursal, fecha, fecha_entrega FROM orden_compra WHERE id = $orden_id";
$result_info_orden = $conn->query($sql_info_orden);

if ($result_info_orden->num_rows > 0) {
    $row_info_orden = $result_info_orden->fetch_assoc();

    // Obtener información del solicitante
    $sql_solicitante = "SELECT solicitante FROM solicitantes WHERE solicitante_id = " . $row_info_orden['solicitante_id'];
    $result_solicitante = $conn->query($sql_solicitante);
    $solicitante_nombre = ($result_solicitante->num_rows > 0) ? $result_solicitante->fetch_assoc()['solicitante'] : 'No encontrado';

    // Obtener información de la sucursal
    $sql_sucursal = "SELECT nombre_sucursal FROM sucursales WHERE id_sucursal = " . $row_info_orden['sucursal'];
    $result_sucursal = $conn->query($sql_sucursal);
    $sucursal_nombre = ($result_sucursal->num_rows > 0) ? $result_sucursal->fetch_assoc()['nombre_sucursal'] : 'No encontrada';

    // Añadir recuadro para la información adicional
    $pdf->SetDrawColor(0, 0, 0); // Color del borde
    $pdf->SetFillColor(200, 220, 255); // Color del fondo
    $pdf->SetLineWidth(0.5); // Ancho del borde
    $pdf->Rect(10, 62, 190, 30, 'DF'); // Dibuja el recuadro para la información adicional

    // Agregar información adicional al recuadro
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(10, 65);

    // Solicitante y Sucursal en una línea
    $info_solicitante_sucursal = 'Solicitante: ' . $solicitante_nombre . '          ' . ' Sucursal: ' . $sucursal_nombre;
    $pdf->MultiCell(0, 10, $info_solicitante_sucursal, 0, 'L');

    // Proveedor en otra línea
    $pdf->Cell(50, 10, 'Proveedor:', 0, 0, 'L');
    $pdf->MultiCell(0, 10, $row_info_orden['proveedor'], 0, 'L');

    // Fecha y Fecha de Entrega en una misma línea
    $info_fecha_entrega = 'Fecha: ' . $row_info_orden['fecha'] . '               ' . '  Fecha de Entrega: ' . $row_info_orden['fecha_entrega'];
    $pdf->MultiCell(0, 10, $info_fecha_entrega, 0, 'L');
}


$sql_detalles = "SELECT * FROM detalle_orden WHERE orden_id = $orden_id";
$result_detalles = $conn->query($sql_detalles);

if ($result_detalles->num_rows > 0) {
    // Agregar tabla de detalles de productos al PDF
    $pdf->Ln(10);

    // Colores de la tabla
    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', 'B', 10);

    // Cabecera de la tabla
    $pdf->Cell(20, 8, 'No', 1, 0, 'C', 1);
    $pdf->Cell(90, 8, 'Descripcion', 1, 0, 'C', 1);
    $pdf->Cell(20, 8, 'Cantidad', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Precio Unitario', 1, 0, 'C', 1);
    $pdf->Cell(30, 8, 'Total', 1, 1, 'C', 1);

    // Datos de la tabla
    $pdf->SetFont('Arial', '', 10);
    while ($row_detalles = $result_detalles->fetch_assoc()) {
        $pdf->Cell(20, 8, $row_detalles["numero"], 1);
        $pdf->Cell(90, 8, $row_detalles["descripcion"], 1);
        $pdf->Cell(20, 8, $row_detalles["cantidad"], 1);
        $pdf->Cell(30, 8, $row_detalles["precio_unitario"], 1);
        $pdf->Cell(30, 8, $row_detalles["total"], 1);
        $pdf->Ln();
    }

    // Calcular el total a pagar, el IVA y el total a pagar con IVA
    $sql_total = "SELECT SUM(total) as total_orden, SUM(total) * 0.16 as iva FROM detalle_orden WHERE orden_id = $orden_id";
    $result_total = $conn->query($sql_total);

    if ($result_total->num_rows > 0) {
        $row_total = $result_total->fetch_assoc();
        $total_orden = $row_total["total_orden"];
        $iva = $row_total["iva"];
        $total_con_iva = $total_orden + $iva;

        // Agregar información al PDF
        $pdf->Ln(0, 'L');

        // Encuadrar la información en recuadros
        $pdf->Cell(50, 7, '', 50, 5); // Salto de línea
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->SetLineWidth(0.5);

        $pdf->Cell(30, 10, 'Subtotal:', 1, 0, 'L', true);
        $pdf->Cell(30, 10, '$' . $total_orden, 1, 1, 'R', true);

        $pdf->Cell(30, 10, 'IVA (16%):', 1, 0, 'L', true);
        $pdf->Cell(30, 10, '$' . $iva, 1, 1, 'R', true);

        $pdf->Cell(30, 10, 'Total a Pagar con IVA:', 1, 0, 'L', true);
        $pdf->Cell(30, 10, '$' . $total_con_iva, 1, 1, 'R', true);

        // Salto de línea después de la sección de total
        $pdf->Ln();
    }
} else {
    $pdf->Ln(50);
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'No hay detalles de productos para esta orden.', 0, 1);
}

// Guardar el PDF en el servidor
$pdf_path = 'pdfs/orden_' . $orden_id . '.pdf';
$pdf->Output($pdf_path, 'F');

// Enviar el PDF al navegador para descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="orden_' . $orden_id . '.pdf"');
readfile($pdf_path);

// Eliminar el PDF del servidor (opcional)
unlink($pdf_path);

// Cerrar la conexión a la base de datos
$conn->close();
?>
