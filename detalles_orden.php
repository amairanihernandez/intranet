p<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    // Si no está autenticado, redirigir al formulario de inicio de sesión
    header('Location: login.php');
    exit();
}

// Obtener información del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'];
$perfil_usuario = $_SESSION['perfil'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Orden</title>
    <style>
        main {
            width: 80%;
            margin: 3%;
            margin-left:10%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            font-family: 'Arial', sans-serif;
            justify-content: center;
            align-items: center;
        }   
        
        header {
            background-color: #72E56B;
            color: black;
            text-align: center;
            padding: 1px;
            border-bottom: 2px solid #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #86ACFF;
            color: #fff;
        }
        td {
            background-color: #fff;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #18C20D;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #18C20D;
        }

        .section-heading {
        background-color: #3498db;
        color: #fff;
        padding: 10px;
        border-radius: 5px;
        margin-top: 20px;
        }

    </style>

</head>

    <?php if ($perfil_usuario === 'admin') : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para administradores -->
        
    <?php else : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para usuarios normales -->
    <?php endif; ?>

<body>

<main>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "orden_compra1";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener el ID de la orden de compra desde la URL
    $orden_id = $_GET['id'];

    // Consulta SQL para obtener detalles de la orden específica
    $sql = "SELECT * FROM orden_compra WHERE id = $orden_id";
    $result = $conn->query($sql);

    $mensaje = '';
    
    // Verificar si la consulta devuelve resultados
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        echo "<header>";
        echo "<h1>Detalles de la Orden</h1>";
        echo "</header>";
        ?>
        <br>
            <button class="button" onclick="window.location.href='autorizacion.php'">
                Volver a la lista de órdenes
            </button>
        <?php
        echo "<table>";
        echo "<tr><th>ID</th><th>Solicitante</th><th>Sucursal</th><th>Proveedor</th><th>Subtotal</th><th>IVA</th><th>Total con IVA</th><th>Fecha solicitud</th><th>Fecha entrega</th><th>Estado</th></tr>";

        // Imprimir los detalles de la orden
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . obtenerNombreSolicitante($row["solicitante_id"], $conn) . "</td>";
        echo "<td>" . obtenerNombreSucursal($row["sucursal"], $conn) . "</td>";
        echo "<td>" . $row["proveedor"] . "</td>";
        echo "<td>" . $row["subtotal"] . "</td>";
        echo "<td>" . $row["iva"] . "</td>";
        echo "<td>" . $row["total_iva"] . "</td>";
        echo "<td>" . $row["fecha"] . "</td>";
        echo "<td>" . $row["fecha_entrega"] . "</td>";
        echo "<td>" . $row["Estado"] . "</td>";
        echo "</tr>";
        echo "</table>";
        
        ?>
        
        <br>
        <?php
        if ($row["Estado"] == "Autorizado") {
            // Generar PDF y proporcionar enlace de descarga
            echo "<header>";
            echo "<h2>Descargar PDF</h2>";
            echo "</header>";
            echo "<br><a class='button' href='generar_pdf.php?id=$orden_id' target='_blank'>Descargar PDF</a><br><br>";
            
        }

        // Consulta SQL para obtener detalles de productos
        $sql_detalles = "SELECT * FROM detalle_orden WHERE orden_id = $orden_id";
        $result_detalles = $conn->query($sql_detalles);

        // Verificar si la consulta de detalles devuelve resultados
        if ($result_detalles->num_rows > 0) {
            echo "<header>";
            echo "<h2>Detalles de Productos</h2>";
            echo "</header>";
            echo "<table>";
            echo "<tr><th>Número</th><th>Descripción</th><th>Cantidad</th><th>Precio Unitario</th><th>Total</th></tr>";

            // Imprimir los detalles de los productos
            while ($row_detalles = $result_detalles->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row_detalles["numero"] . "</td>";
                echo "<td>" . $row_detalles["descripcion"] . "</td>";
                echo "<td>" . $row_detalles["cantidad"] . "</td>";
                echo "<td>" . $row_detalles["precio_unitario"] . "</td>";
                echo "<td>" . $row_detalles["total"] . "</td>";
                echo "</tr>";
            }

            echo "</table>";

            
            // Formulario para cambiar el estado
            echo "<br><header>";
            echo "<h2>Cambiar Estado</h2>";
            echo "</header>";
            echo "<form action='cambiar_estado2.php' method='post'>";
            echo "<input type='hidden' name='orden_id' value='$orden_id'><br>";
            

            // Verificar el usuario y mostrar el campo de código solo para el usuario correspondiente
            $usuario_id = 1; // Este valor debería obtenerse dinámicamente según el usuario actual

            function tuFuncionDeConsulta($query) {
                // Reemplaza 'tu_usuario', 'tu_contrasena', 'tu_bd' con tus propias credenciales
                $conn = new mysqli('localhost', 'root', '', 'orden_compra1');
                
                // Verifica la conexión
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }
                
                // Ejecuta la consulta
                $result = $conn->query($query);
                
                // Cierra la conexión
                $conn->close();
                
                return $result;
            }
            
            // ...
            
            if ($row["total_iva"] <= 5000 && $usuario_id == 1) {
                $checked0 = ($row["Autorizado_1"] == 'Autorizado') ? 'checked' : '';
                $codigoUsuario1 = isset($row['codigo_usuario_1']) ? $row['codigo_usuario_1'] : '';
                
                echo "<label><input type='checkbox' name='autorizado[0]' value='autorizado' $checked0 onchange='mostrarOcultarCodigoUsuario(1)'> ▶ Autorizar hasta $5000</label><br>";
                
                if ($row["Autorizado_1"] == 'Autorizado') {
                    // Realiza la consulta a la base de datos para obtener el código del usuario
                    $usuario_id = 1; // Ajusta el valor según tu lógica de usuario
                    $query = "SELECT codigo FROM usuarios WHERE id = $usuario_id";
                    // Ejecuta la consulta y obtén el resultado (asegúrate de usar prácticas seguras como prepared statements)
                    $result = tuFuncionDeConsulta($query);
                    
                    if ($result && $rowUsuario = mysqli_fetch_assoc($result)) {
                        $codigoUsuario1FromDB = $rowUsuario['codigo'];
                        echo "<div id='codigo_usuario_1'>Código de Usuario 1: ";
                        echo "<input type='password' name='codigo_usuario[1]' id='codigo_usuario_1_input' value='$codigoUsuario1FromDB' readonly>";
                        echo "</div>";
                    } else {
                        // Manejar el caso en el que no se pudo obtener el código del usuario
                        echo "Error al obtener el código del usuario.";
                    }
                } else {
                    // Si no está autorizado, mostrar el input de código como lo haces actualmente
                    echo "<div id='codigo_usuario_1'>Código de Usuario 1: ";
                    echo "<input type='password' name='codigo_usuario[1]' id='codigo_usuario_1_input' value='$codigoUsuario1'>";
                    echo "</div>";
                }
            }
            

            // ...

            // Bloque 1
            if ($row["total_iva"] > 5000 && $row["total_iva"] <= 20000 && in_array($usuario_id, [1, 2])) {
                $checked1 = ($row["Autorizado_1"] == 'Autorizado') ? 'checked' : '';
                $checked2 = ($row["Autorizado_2"] == 'Autorizado') ? 'checked' : '';
                $codigoUsuario1 = isset($row['codigo_usuario_1']) ? $row['codigo_usuario_1'] : '';
                $codigoUsuario2 = isset($row['codigo_usuario_2']) ? $row['codigo_usuario_2'] : '';
                
                echo "<label><input type='checkbox' name='autorizado[1]' value='autorizado' $checked1> ▶ Autorizar hasta de $5000</label><br>";
                if ($row["Autorizado_1"] == 'Autorizado') {
                    // Realiza la consulta a la base de datos para obtener el código del usuario
                    $usuario_id = 1; // Ajusta el valor según tu lógica de usuario
                    $query = "SELECT codigo FROM usuarios WHERE id = $usuario_id";
                    // Ejecuta la consulta y obtén el resultado (asegúrate de usar prácticas seguras como prepared statements)
                    $result = tuFuncionDeConsulta($query);
                    
                    if ($result && $rowUsuario = mysqli_fetch_assoc($result)) {
                        $codigoUsuario1FromDB = $rowUsuario['codigo'];
                        echo "<div id='codigo_usuario_1'>Código de Usuario 1: ";
                        echo "<input type='password' name='codigo_usuario[1]' id='codigo_usuario_1_input' value='$codigoUsuario1FromDB' readonly>";
                        echo "</div>";
                    } else {
                        // Manejar el caso en el que no se pudo obtener el código del usuario
                        echo "Error al obtener el código del usuario.";
                    }
                } else {
                    // Si no está autorizado, mostrar el input de código como lo haces actualmente
                    echo "<div id='codigo_usuario_1'>Código de Usuario 1: ";
                    echo "<input type='password' name='codigo_usuario[1]' id='codigo_usuario_1_input' value='$codigoUsuario1'>";
                    echo "</div>";
                }
                
                echo "<label><input type='checkbox' name='autorizado[2]' value='autorizado' $checked2> ▶ Autorizar hasta de $20000</label><br>";
                if ($row["Autorizado_2"] == 'Autorizado') {
                    // Realiza la consulta a la base de datos para obtener el código del usuario
                    $usuario_id = 2; // Ajusta el valor según tu lógica de usuario
                    $query = "SELECT codigo FROM usuarios WHERE id = $usuario_id";
                    // Ejecuta la consulta y obtén el resultado (asegúrate de usar prácticas seguras como prepared statements)
                    $result = tuFuncionDeConsulta($query);
                    
                    if ($result && $rowUsuario = mysqli_fetch_assoc($result)) {
                        $codigoUsuario2FromDB = $rowUsuario['codigo'];
                        echo "<div id='codigo_usuario_2'>Código de Usuario 2: ";
                        echo "<input type='password' name='codigo_usuario[2]' id='codigo_usuario_2_input' value='$codigoUsuario2FromDB' readonly>";
                        echo "</div>";
                    } else {
                        // Manejar el caso en el que no se pudo obtener el código del usuario
                        echo "Error al obtener el código del usuario.";
                    }
                } else {
                    // Si no está autorizado, mostrar el input de código como lo haces actualmente
                    echo "<div id='codigo_usuario_2'>Código de Usuario 2: ";
                    echo "<input type='password' name='codigo_usuario[2]' id='codigo_usuario_2_input' value='$codigoUsuario2'>";
                    echo "</div>";
                }
            }

            // Bloque 2
            if ($row["total_iva"] > 20000 && in_array($usuario_id, [1, 2, 3])) {
                $checked1 = ($row["Autorizado_1"] == 'Autorizado') ? 'checked' : '';
                $checked2 = ($row["Autorizado_2"] == 'Autorizado') ? 'checked' : '';
                $checked3 = ($row["Autorizado_3"] == 'Autorizado') ? 'checked' : '';
                $codigoUsuario1 = isset($row['codigo_usuario_1']) ? $row['codigo_usuario_1'] : '';
                $codigoUsuario2 = isset($row['codigo_usuario_2']) ? $row['codigo_usuario_2'] : '';
                $codigoUsuario3 = isset($row['codigo_usuario_3']) ? $row['codigo_usuario_3'] : '';
                
                echo "<label><input type='checkbox' name='autorizado[3]' value='autorizado' $checked1> ▶ Autorizar hasta de $5000</label><br>";
                if ($row["Autorizado_1"] == 'Autorizado') {
                    // Realiza la consulta a la base de datos para obtener el código del usuario
                    $usuario_id = 1; // Ajusta el valor según tu lógica de usuario
                    $query = "SELECT codigo FROM usuarios WHERE id = $usuario_id";
                    // Ejecuta la consulta y obtén el resultado (asegúrate de usar prácticas seguras como prepared statements)
                    $result = tuFuncionDeConsulta($query);
                    
                    if ($result && $rowUsuario = mysqli_fetch_assoc($result)) {
                        $codigoUsuario1FromDB = $rowUsuario['codigo'];
                        echo "<div id='codigo_usuario_1'>Código de Usuario 1: ";
                        echo "<input type='password' name='codigo_usuario[1]' id='codigo_usuario_1_input' value='$codigoUsuario1FromDB' readonly>";
                        echo "</div>";
                    } else {
                        // Manejar el caso en el que no se pudo obtener el código del usuario
                        echo "Error al obtener el código del usuario.";
                    }
                } else {
                    // Si no está autorizado, mostrar el input de código como lo haces actualmente
                    echo "<div id='codigo_usuario_1'>Código de Usuario 1: ";
                    echo "<input type='password' name='codigo_usuario[1]' id='codigo_usuario_1_input' value='$codigoUsuario1'>";
                    echo "</div>";
                }
                
                echo "<label><input type='checkbox' name='autorizado[4]' value='autorizado' $checked2> ▶ Autorizar hasta de $20000</label><br>";
                if ($row["Autorizado_2"] == 'Autorizado') {
                    // Realiza la consulta a la base de datos para obtener el código del usuario
                    $usuario_id = 2; // Ajusta el valor según tu lógica de usuario
                    $query = "SELECT codigo FROM usuarios WHERE id = $usuario_id";
                    // Ejecuta la consulta y obtén el resultado (asegúrate de usar prácticas seguras como prepared statements)
                    $result = tuFuncionDeConsulta($query);
                    
                    if ($result && $rowUsuario = mysqli_fetch_assoc($result)) {
                        $codigoUsuario2FromDB = $rowUsuario['codigo'];
                        echo "<div id='codigo_usuario_2'>Código de Usuario 2: ";
                        echo "<input type='password' name='codigo_usuario[2]' id='codigo_usuario_2_input' value='$codigoUsuario2FromDB' readonly>";
                        echo "</div>";
                    } else {
                        // Manejar el caso en el que no se pudo obtener el código del usuario
                        echo "Error al obtener el código del usuario.";
                    }
                } else {
                    // Si no está autorizado, mostrar el input de código como lo haces actualmente
                    echo "<div id='codigo_usuario_2'>Código de Usuario 2: ";
                    echo "<input type='password' name='codigo_usuario[2]' id='codigo_usuario_2_input' value='$codigoUsuario2'>";
                    echo "</div>";
                }         

                echo "<label><input type='checkbox' name='autorizado[5]' value='autorizado' $checked3> ▶ Autorizar mas de $20000</label><br>";
                if ($row["Autorizado_3"] == 'Autorizado') {
                    // Realiza la consulta a la base de datos para obtener el código del usuario
                    $usuario_id = 3; // Ajusta el valor según tu lógica de usuario
                    $query = "SELECT codigo FROM usuarios WHERE id = $usuario_id";
                    // Ejecuta la consulta y obtén el resultado (asegúrate de usar prácticas seguras como prepared statements)
                    $result = tuFuncionDeConsulta($query);
                    
                    if ($result && $rowUsuario = mysqli_fetch_assoc($result)) {
                        $codigoUsuario3FromDB = $rowUsuario['codigo'];
                        echo "<div id='codigo_usuario_3'>Código de Usuario 3: ";
                        echo "<input type='password' name='codigo_usuario[3]' id='codigo_usuario_3_input' value='$codigoUsuario3FromDB' readonly>";
                        echo "</div>";
                    } else {
                        // Manejar el caso en el que no se pudo obtener el código del usuario
                        echo "Error al obtener el código del usuario.";
                    }
                } else {
                    // Si no está autorizado, mostrar el input de código como lo haces actualmente
                    echo "<div id='codigo_usuario_3'>Código de Usuario 3: ";
                    echo "<input type='password' name='codigo_usuario[3]' id='codigo_usuario_3_input' value='$codigoUsuario3'>";
                    echo "</div>";
                } 
            }


            echo "<input type='submit' value='Guardar Estado'>";
            echo "</form>";


        } else {
            echo "No hay detalles de productos para esta orden.";
        }
    } else {
        echo "Orden no encontrada";
    }

    // Cerrar la conexión
    $conn->close();

    function obtenerNombreSucursal($sucursal_id, $conn) {
        $sql = "SELECT nombre_sucursal FROM sucursales WHERE id_sucursal = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $stmt->bind_result($nombre_sucursal);
    
        if ($stmt->fetch()) {
            return $nombre_sucursal;
        } else {
            return "Sucursal no encontrada";
        }
    
        $stmt->close();
    }
    function obtenerNombreSolicitante($solicitante_id, $conn) {
        $sql = "SELECT solicitante FROM solicitantes WHERE solicitante_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $solicitante_id);
        $stmt->execute();
        $stmt->bind_result($solicitante);
    
        if ($stmt->fetch()) {
            return $solicitante;
        } else {
            return "Sucursal no encontrada";
        }
    
        $stmt->close();
    }
    ?>
    </main>
    <br>
        
        <br><br><br><br>
</body>
</html>

