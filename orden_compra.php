<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "orden_compra1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$solicitante_id = $_POST['solicitante'];
$proveedor = $_POST['proveedor'];
$sucursal = $_POST['sucursal_id'];
$fecha = $_POST['fecha'];
$fecha_entrega = $_POST['fecha_entrega'];
$sub_total = $_POST['Subtotal'];
$iva = $_POST['iva'];
$Estado = 'Revision';

$total_iva = $sub_total + ($sub_total * (16 / 100));

// Insertar en la base de datos
$sql = "INSERT INTO orden_compra (solicitante_id, proveedor, sucursal, fecha, fecha_entrega, subtotal, iva, total_iva, Estado)
        VALUES ('$solicitante_id', '$proveedor', '$sucursal','$fecha', '$fecha_entrega', '$sub_total', '$iva', '$total_iva', '$Estado')";
if ($conn->query($sql) === TRUE) {
    $orden_id = $conn->insert_id;

    foreach ($_POST['numero'] as $key => $value) {
        $numero = $_POST['numero'][$key];
        $descripcion = $_POST['descripcion'][$key];
        $cantidad = $_POST['cantidad'][$key];
        $precio_unitario = $_POST['precio'][$key];
        $total = $_POST['total'][$key];

        $sql = "INSERT INTO detalle_orden (orden_id, numero, descripcion, cantidad, precio_unitario, total)
                VALUES ('$orden_id', '$numero', '$descripcion', '$cantidad', '$precio_unitario','$total')";

        $conn->query($sql);
    }

    // Imprime un mensaje de éxito con enlace para descargar el PDF
    echo "Orden de compra creada con éxito.";
} else {
    echo "Error al crear la orden de compra: " . $conn->error;
}
        

$conn->close();
?>
        <button onclick="window.location.href='autorizacion.php'">
            Listado de solicitudes
        </button>