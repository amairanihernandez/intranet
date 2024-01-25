<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .perfil {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .campo {
            margin-bottom: 15px;
        }
        .campo label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="perfil">
    <h1>Mi Perfil</h1>

    <div class="campo">
        <label for="nombre">Nombre:</label>
        <p id="nombre">Juan Pérez</p>
    </div>

    <div class="campo">
        <label for="edad">Edad:</label>
        <p id="edad">25 años</p>
    </div>

    <div class="campo">
        <label for="correo">Correo electrónico:</label>
        <p id="correo">juan.perez@example.com</p>
    </div>

    <!-- Puedes agregar más campos según sea necesario -->

</div>

</body>
</html>
