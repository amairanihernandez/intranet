<?php
    // Conexión a la base de datos (ajusta los parámetros según tu configuración)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "orden_compra1";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener el folio del empleado desde la solicitud AJAX
    $folio = $_GET['folio'];

    // Consultar la base de datos para obtener los datos del empleado
    $query = "SELECT * FROM empleados WHERE folio = $folio";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Obtener el nombre del gerente usando el ID del gerente almacenado en la tabla empleados
        $idGerente = $row['gerente'];
        $queryGerente = "SELECT gerente FROM gerente WHERE Id_gerente = $idGerente";
        $resultGerente = $conn->query($queryGerente);
        $rowGerente = $resultGerente->fetch_assoc();

        // Obtener el nombre del departamento usando el ID del departamento almacenado en la tabla empleados
        $idDepartamento = $row['departamento'];
        $queryDepartamento = "SELECT deparatamento FROM departamento WHERE Id_departamento = $idDepartamento";
        $resultDepartamento = $conn->query($queryDepartamento);
        $rowDepartamento = $resultDepartamento->fetch_assoc(); 

        // Crear un array con los datos del empleado
        $empleado = array(
            'nombre' => $row['nombre'],
            'salario_diario' => $row['salario_diario'],
            'puesto' => $row['puesto'],
            'departamento' => $rowDepartamento['deparatamento'],
            'fecha_ingreso' => $row['fecha_ingreso'],
            'gerente' => $rowGerente['gerente']
        );

        // Devolver los datos en formato JSON
        echo json_encode($empleado);
    } else {
        // Si no se encuentra el empleado, devolver un array vacío
        echo json_encode(array());
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
?>
