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
    <title>Página Principal</title>
    <link rel="stylesheet" type="text/css" href="./styles/estil.css">
</head>
<body>


    <?php if ($perfil_usuario === 'admin') : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para administradores -->
        
    <?php else : ?>
        <?php include "header.php"; ?>
        <!-- Contenido específico para usuarios normales -->
    <?php endif; ?>
    <section id="contenido">
        
    <?php include "fond.php"; ?>
        <div>
            <img src="img/img1.png" alt="Imagen 1">
        </div>
        <div class="text-container">
        <div class="text-box" style="font-family: 'Times New Roman', serif;">
            <p>
               <h2> ¿Quiénes somos?</h2>
               Somos una Cooperativa de Ahorro y Préstamo no lucrativa con 55 años de respaldo, 
               formada por personas que se han unido voluntariamente para hacer frente a sus necesidades económicas, 
               sociales y culturales mediante la cultura del ahorro y el manejo responsable de los préstamos,
               dentro de una empresa de propiedad conjunta y democráticamente controlada.
            </p>

            <p>
                <h4>Mision</h4>
                Somos una Cooperativa encaminada al mejoramiento de las condiciones de vida de nuestros socios a 
                través de la prestación de servicios financieros de ahorro y 
                préstamos basados en los principios y valores cooperativos.
            </p>

            <p>
                <h4>Vision</h4>
                Ser una Cooperativa líder y consolidar los servicios que ofrece en la región noreste por medio de una infraestructura económica y humana apropiada, 
                contando con tecnologías y sistemas que nos permita atender con calidad, e impulse a mejorar continuamente los procesos y el desarrollo integral de nuestros socios.
            </p>

            <p>
                <h4>Mision</h4>
                Ayuda mutua. <br>
                -Responsabilidad: deberes, obligaciones, capacidad para responder a los propios actos y de sus consecuencias. <br>
                -Democracia: gobierno del pueblo, por el pueblo y para el pueblo. <br>
                -Igualdad: no privilegios, un socio un voto. <br>
                -Equidad: distribución económica de los beneficios de la Cooperativa. <br>
                -Transparencia: claridad. <br>
                -Honestidad: solvencia moral. <br>
                -Solidaridad: adhesión circunstancial a la causa de otros. <br>
            </p>
        </div>
        </div>
    </section>

    <?php include('foother.php'); ?>
    
</body>
</html>
