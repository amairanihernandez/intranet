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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Formulario de Solicitud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        form {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-family: "Times New Roman", serif;
        }

        input[type="text"],
        input[type="submit"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            font-family: "Times New Roman", serif;
        }

        input[type="submit"] {
            background-color: #18C20D;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 16px;
        }

        .section-heading,
        .subsection-heading {
            background-color: #72E56B;
            color: black;
            padding: 8px;
            margin-top: 15px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .row label {
            flex-basis: 48%; /* Ajusta según el espacio que desees entre las etiquetas */
        }

        textarea {
            resize: vertical;
        }
    </style>

    
</head>
    <?php if ($perfil_usuario === "3") : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para administradores -->
        
    <?php else : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para usuarios normales -->
    <?php endif; ?>
<body>
    

    <form action="procesar_solicitud.php" method="post">
        <!-- Campos generales -->
        <div class="row">
            <label>Fecha: <input type="text" name="fecha" value="<?php echo date('Y-m-d'); ?>" readonly></label><br>
            <label>Folio: <input type="text" name="folio" required>
            <button type="button" onclick="buscarEmpleado()" style="background-color: #18C20D; color: black; padding: 10px; border: none; border-radius: 5px; cursor: pointer; font-family: Times New Roman, serif;">Buscar</button>
            </label>
        </div>

        <!-- Datos del trabajador -->
        <div class="container">
            <div class="section-heading" style="background-color: #72E56B; color: black; padding: 8px;">Datos del Trabajador</div>


            <label class="subsection-heading" style="background-color: #72E56B; color: black; padding: 8px;">Información Personal</label>
            <div class="row">

                <label>Nombre del trabajador: <input type="text" name="nombre_trabajador" id="nombre_trabajador" required></label>
                <label>Salario diario: <input type="text" name="salario_diario" id="salario_diario" required></label><br>

                <label>Puesto: <input type="text" name="puesto" id="puesto" required></label>
                <label>Departamento: <input type="text" name="departamento" id="departamento" required></label><br>

                <label>Fecha de Ingreso: <input type="text" name="fecha_ingreso" id="fecha_ingreso" required></label>
                <label>Días a disfrutar: <input type="text" name="dias_disfrutar" required></label>
            </div>


            <label class="subsection-heading" style="background-color: #72E56B; color: black; padding: 8px;">Solicitud de Días</label>
            <div class="row">

                <label>Periodo a disfrutar del: <input type="date" name="fecha_inicio" required></label>
                <label>Al: <input type="date" name="fecha_fin" required></label><br>

                <label>Días restantes por disfrutar: <input type="text" name="dias_restantes" id="dias_restantes" required readonly></label>
                <label>Regresa el día: <input type="text" name="regresa_dia" id="regresa_dia" required readonly></label>
                <label>Fecha próxima a salir: <input type="text" name="fecha_proxima_salida" id="fecha_proxima_salida" required readonly></label>

                <label>Solicitud pago en efectivo contra el disfrute de las mismas: 
                    <input type="text" name="solicitud_pago" value="Si" required>
                </label>
            </div>


            <label class="subsection-heading" style="background-color: #72E56B; color: black; padding: 8px;">Detalles de Pago y Tiempo</label>
            <div class="row">

                <label>Días pagados: <input type="text" name="dias_pagados" required></label>
                <label>Días pendientes: <input type="text" name="dias_pendientes" required></label><br>
            </div>


            <label class="subsection-heading" style="background-color: #72E56B; color: black; padding: 8px;">Motivo y Comentarios</label>
            <div class="row">

                <label>Motivo: <input type="text" name="motivo" required></label><br>
                <label>Comentarios: <textarea name="comentarios" rows="4" cols="50"></textarea></label><br>
            </div>

            <!-- Firmas -->
            <label class="subsection-heading" style="background-color: #72E56B; color: black; padding: 8px;">Firmas</label>

            <div class="row">
                <label>Firma del solicitante: <input type="text" name="firma_solicitante" required></label>
                <label>Firma del gerente de área: <input type="text" name="firma_gerente" id="gerente" required></label>
            </div>
        </div>

        <!-- Botón de enviar -->
        <div class="boton">
            <input type="submit" value="Enviar" style="background-color: #18C20D; color: black; font-family: Times New Roman, serif;">
        </div>
    </form>
    
    <script>
        function buscarEmpleado() {
            var folio = document.getElementsByName("folio")[0].value;

            // Realizar una petición AJAX para obtener los datos del empleado
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var empleado = JSON.parse(this.responseText);

                    // Rellenar los campos del formulario con los datos del empleado
                    document.getElementById("nombre_trabajador").value = empleado.nombre;
                    document.getElementById("salario_diario").value = empleado.salario_diario;
                    document.getElementById("puesto").value = empleado.puesto;
                    document.getElementById("departamento").value = empleado.departamento;
                    document.getElementById("fecha_ingreso").value = empleado.fecha_ingreso;
                    document.getElementById("gerente").value = empleado.gerente;
                }
            };

            // Hacer la solicitud al script PHP que obtiene los datos del empleado
            xhr.open("GET", "obtener_datos_empleado.php?folio=" + folio, true);
            xhr.send();
        }
    </script>


<script>
    // Obtener los elementos de fecha
    const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
    const fechaFin = document.querySelector('input[name="fecha_fin"]');
    const diasRestantes = document.getElementById('dias_restantes');
    const regresaDia = document.getElementById('regresa_dia');
    const fechaProximaSalida = document.getElementById('fecha_proxima_salida');

    // Event listener para calcular días restantes y actualizar campos
    fechaInicio.addEventListener('input', calcularDiasRestantes);
    fechaFin.addEventListener('input', calcularDiasRestantes);

    function calcularDiasRestantes() {
        const fechaInicioValue = new Date(fechaInicio.value);
        const fechaFinValue = new Date(fechaFin.value);

        // Calcular la diferencia en milisegundos
        const diferenciaTiempo = fechaFinValue - fechaInicioValue;

        // Convertir la diferencia a días
        const diasRestantesValue = Math.ceil(diferenciaTiempo / (1000 * 60 * 60 * 24));

        // Mostrar días restantes
        diasRestantes.value = diasRestantesValue;

        // Calcular y mostrar la fecha de regreso
        const regresoValue = new Date(fechaInicioValue);
        regresoValue.setDate(regresoValue.getDate() + diasRestantesValue);
        regresaDia.value = regresoValue.toISOString().split('T')[0];

        // Calcular y mostrar la fecha próxima a salir
        const proximaSalidaValue = new Date(fechaFinValue);
        proximaSalidaValue.setDate(proximaSalidaValue.getDate() + 1);
        fechaProximaSalida.value = proximaSalidaValue.toISOString().split('T')[0];
    }
</script>

<?php include('foother.php'); ?>
    
</body>
</html>
