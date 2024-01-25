<?php
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
    <title>Consulta de Órdenes de Compra</title>
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
            font-family: "Times New Roman", serif;
        }

        h1 {
            margin: 0;
            font-size: 2em;
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'Times New Roman', serif;
        }

        th, td {
            border: 2px solid #ddd;
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        td {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
        }

        a:hover {
            color: #21618c;
        }
        form {
            text-align: center;
            margin-top: 20px;
            font-family: 'Times New Roman', serif;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
        }
    </style>
</head>

<?php if ($perfil_usuario === "3") : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para administradores -->
        
    <?php elseif ($perfil_usuario === "2")  : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para usuarios normales -->
    <?php endif; ?>

<body>
    <main>
        <header>
            <h1>Órdenes de Compra y Requisición</h1>
        </header>
        
        <form method="GET" action="">
            <label for="search">Buscar:</label>
            <input type="text" id="search" name="search" placeholder="Ingrese término de búsqueda" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit" style="font-family: 'Times New Roman', serif;">Buscar</button>
        </form>

        <button onclick="window.location.href='orden.php'" style="font-family: 'Times New Roman', serif;">
            Generar Solicitud
        </button>
        
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "orden_compra1";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        if (isset($_GET['search'])) {
            $searchTerm = $_GET['search'];
        
            // Modified SQL query to include JOIN operations
            $sql = "SELECT oc.*, s.solicitante AS solicitante_nombre, suc.nombre_sucursal 
                    FROM orden_compra oc
                    LEFT JOIN solicitantes s ON oc.solicitante_id = s.solicitante_id
                    LEFT JOIN sucursales suc ON oc.sucursal = suc.id_sucursal
                    WHERE 
                    s.solicitante LIKE '%$searchTerm%' OR 
                    suc.nombre_sucursal LIKE '%$searchTerm%' OR 
                    oc.Estado LIKE '%$searchTerm%'";
        } else {
            $sql = "SELECT oc.*, s.solicitante AS solicitante_nombre, suc.nombre_sucursal 
                    FROM orden_compra oc
                    LEFT JOIN solicitantes s ON oc.solicitante_id = s.solicitante_id
                    LEFT JOIN sucursales suc ON oc.sucursal = suc.id_sucursal";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Solicitante</th><th>Proveedor</th><th>Sucursal</th><th>Subtotal</th><th>IVA</th><th>Total con IVA</th><th>Fecha solicitud</th><th>Fecha entrega</th><th>Estado</th><th>Detalles</th></tr>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . obtenerNombreSolicitante($row["solicitante_id"], $conn) . "</td>";
                echo "<td>" . $row["proveedor"] . "</td>";
                echo "<td>" . obtenerNombreSucursal($row["sucursal"], $conn) . "</td>";
                echo "<td>$" . $row["subtotal"] . "</td>";
                echo "<td>$" . $row["iva"] . "</td>";
                echo "<td>$" . $row["total_iva"] . "</td>";
                echo "<td>" . $row["fecha"] . "</td>";
                echo "<td>" . $row["fecha_entrega"] . "</td>";
                echo "<td>" . $row["Estado"]  . "</td>";
                echo "<td><a href='detalles_orden.php?id=" . $row["id"] . "'>Ver Detalles</a></td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No hay resultados.</p>";
        }

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
</body>
</html>
