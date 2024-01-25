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
    <div class="header1">
        <?php if ($perfil_usuario === "3") : ?>
            <?php include "header.php"; ?>
            <!-- Contenido específico para administradores -->
            
        <?php else : ?>
            <?php include "header.php"; ?>
            <!-- Contenido específico para usuarios normales -->
        <?php endif; ?>
    </div>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orden de compra o requisicion</title>

    <style>
        main {
            font-family: "Times New Roman", serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 110vh;
        }

        header {
            background-color: #06e475;
            color: #000;
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #2980b9;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            
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
        label {
            margin-botton: 10px;
            
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        input{
            font-family: "Times New Roman", serif;
        }
    </style>
</head>
    
<body>
    
<main>

    <form action="orden_compra.php" method="post">
        <header >
            <h1>Generar Órden de Compra y Requisición</h1>
        </header>

        <br>

        <button onclick="window.location.href='autorizacion.php'" style="font-family: 'Times New Roman', serif;">
            Listado de solicitudes
        </button>

        <br><br>
        <div style="display:flex; aling-items: center;">
        

            <label for="id_solicitante">Solicitante</label>
                <select name="solicitante" id="id_solicitante" style="font-family: 'Times New Roman', serif;">
                    <option value="1">Contabilidad</option>
                    <option value="2">Sistemas</option>
                    <option value="3">Operaciones</option>
                    <option value="4">Mesa de control</option>
                    <option value="5">Administracion</option>
                    <option value="6">Recursos Humanos</option>
                    <option value="7">Juridico</option>
                    <option value="8">Cumplimiento</option>
                    <option value="9">Cobranza</option>
                    <option value="10">Asistente de Gerencia</option>
                    <option value="11">Tesoreria</option>
                </select>
                
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "orden_compra1";
                
                $conn = new mysqli($servername, $username, $password, $database);
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }
                // Verificar la conexión
                $sql = "SELECT id_sucursal, nombre_sucursal FROM sucursales";
                $result = $conn->query($sql);

                // Crear el elemento select
                echo "<label for='Sucursal'>Sucursal</label>";
                echo '<select name="sucursal_id">';
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id_sucursal'] . '">' . $row['nombre_sucursal'] . '</option>';
                }
                echo '</select>';
                ?>
                

        </div> 
        
        

        <br><br>
        <label for="fecha">Fecha</label>
        <input type="date" name="fecha" id="fecha" value="<?php echo date('Y-m-d'); ?>" readonly>


        <label for="fecha_entrega">Fecha de entrega</label>
        <input type="date" name="fecha_entrega" id="fecha_entrega" required>

        <br><br>
        <table>
        <label for="id_proveedor">Proveedor</label>
        <input type="text" name="proveedor" id="id_proveedor" required>
            <tr>
                <th>Numero</th>
                <th>Descripcion</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>Total</th>
            </tr>
            <tr>
                <td><input type="number" name="numero[]" class="numero" readonly></td>
                <td><input type="text" name="descripcion[]" class="descripcion"></td>
                <td><input type="number" name="cantidad[]" class="cantidad" oninput="operacion(this)"></td>
                <td><input type="number" name="precio[]" class="precio" oninput="operacion(this)"></td>
                <td><input type="number" name="total[]" class="total" value="total" readonly></td>
            </tr>
            <?php for ($i = 2; $i <= 13; $i++) { ?>
                <tr>
                    <td><input type="number" name="numero[]" class="numero" readonly></td>
                    <td><input type="text" name="descripcion[]" class="descripcion"></td>
                    <td><input type="number" name="cantidad[]" class="cantidad" oninput="operacion(this)"></td>
                    <td><input type="number" name="precio[]" class="precio" oninput="operacion(this)"></td>
                    <td><input type="number" name="total[]" class="total" value="total" readonly></td>
                </tr>
            <?php } ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="1" style="text-align:right;">Subtotal</td>
                <td><input type="number" name="Subtotal" id="Subtotal" readonly></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="1" style="text-align:right;">IVA</td>
                <td><input type="number" name="iva" id="iva" readonly></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="1" style="text-align:right;">Total a pagar</td>
                <td><input type="number" name="totalIva" id="totalIva" readonly></td>
            </tr>
        </table>

        
        <br><br>
        <input type="submit" value="Generar Orden de Compra">

    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setInitialValues();

            document.getElementById("id_solicitante").addEventListener("change", function () {
                setInitialValues();
            });
        });

        function setInitialValues() {
            var rows = document.querySelectorAll(".numero");
            var solicitante = document.getElementById("id_solicitante").value;

            rows.forEach(function (row, index) {
                row.value = index + 1;
            });
        }

        function operacion(input) {
            var row = input.closest("tr");
            var cantidad = row.querySelector(".cantidad").value;
            var precio = row.querySelector(".precio").value;
            var total = cantidad * precio;
            row.querySelector(".total").value = total;

            Subtotal();
            totalIva();
        }

        function Subtotal() {
            var totals = document.querySelectorAll('.total');
            var Subtotal = 0;

            totals.forEach(function (total) {
                Subtotal += parseFloat(total.value) || 0;
            });

            document.getElementById('Subtotal').value = Subtotal;
        }

        function totalIva() {
            var Subtotal = parseFloat(document.getElementById('Subtotal').value) || 0;
            var iva = Subtotal * 0.16 || 0;
            var totalIva = Subtotal + iva || 0;

            document.getElementById('iva').value = iva;
            document.getElementById('totalIva').value = totalIva;
        }

        
    </script>
    </main>
</body>

</html>
