<?php
// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos (ajusta los parámetros según tu configuración)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "orden_compra1";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener el folio ingresado
    $folio = $_POST['folio'];

    // Consultar la base de datos para obtener los datos del empleado
    $query = "SELECT * FROM empleados WHERE folio = $folio";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Datos del empleado
        $nombre_trabajador = $row['nombre'];
        $salario_diario = $row['salario_diario'];
        $puesto = $row['puesto'];
        $departamento = $row['departamento'];
        $fecha_ingreso = $row['fecha_ingreso'];
        $firma_gerente = $row['gerente'];

        // Otros datos del formulario
        // Asegúrate de ajustar estos nombres según los campos del formulario
        $dias_disfrutar = $_POST['dias_disfrutar'];
        $periodo_disfrutar = $_POST['periodo_disfrutar'];
        $dias_restantes = $_POST['dias_restantes'];
        $regresa_dia = $_POST['regresa_dia'];
        $fecha_proxima_salida = $_POST['fecha_proxima_salida'];
        $solicitud_pago = $_POST['solicitud_pago'];
        $dias_pagados = $_POST['dias_pagados'];
        $dias_pendientes = $_POST['dias_pendientes'];
        $motivo = $_POST['motivo'];
        $comentarios = $_POST['comentarios'];
        $firma_solicitante = $_POST['firma_solicitante'];
        //$firma_gerente = $_POST['firma_gerente'];

        // Insertar los datos en la base de datos (ajusta la consulta según tu estructura)
        $insert_query = "INSERT INTO solicitud (dias_disfrutar, periodo_disfrutar, dias_restantes, regresa_dia, fecha_proxima_salida, solicitud_pago, dias_pagados, dias_pendientes, motivo, comentarios, firma_solicitante, firma_gerente) VALUES ('$dias_disfrutar', '$periodo_disfrutar', '$dias_restantes', '$regresa_dia', '$fecha_proxima_salida', '$solicitud_pago', '$dias_pagados', '$dias_pendientes', '$motivo', '$comentarios', '$firma_solicitante', '$firma_gerente')";

        if ($conn->query($insert_query) === TRUE) {
            echo "Solicitud enviada correctamente.";
        } else {
            echo "Error al enviar la solicitud: " . $conn->error;
        }
    } else {
        echo "Empleado no encontrado";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>
