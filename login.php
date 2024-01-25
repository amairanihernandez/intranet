<?php
session_start();

// Verificar si el usuario ya está autenticado y redirigirlo a la página principal
if (isset($_SESSION['idusuario'])) {
    header('Location: index.php');
    exit();
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conectar a la base de datos (reemplazar con tus datos de conexión)
    $conexion = new mysqli('localhost', 'root', '', 'orden_compra1');

    // Verificar la conexión
    if ($conexion->connect_error) {
        die('Error de conexión: ' . $conexion->connect_error);
    }

    // Escapar las variables del formulario para prevenir SQL injection
    $nombre_usuario = $conexion->real_escape_string($_POST['nombre_usuario']);
    $contrasena = $conexion->real_escape_string($_POST['contrasena']);

    // Consulta para obtener el usuario y la contraseña correspondientes
    $consulta = "SELECT idusuario, nombre, perfil, idorigen, passwd FROM users WHERE idusuario = '$nombre_usuario' AND activo = 't'";
    $resultado = $conexion->query($consulta);

    if ($resultado->num_rows === 1) {
        // Usuario encontrado, verificar la contraseña
        $usuario = $resultado->fetch_assoc();

        if (password_verify($contrasena, $usuario['passwd'])) {
            // Contraseña verificada con éxito
            // Almacenar información del usuario en la sesión
            $_SESSION['idusuario'] = $usuario['idusuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['perfil'] = $usuario['perfil'];

            // Redirigir a la página principal
            header('Location: index.php');
            exit();
        } else {
            $error = 'Nombre de usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Nombre de usuario o contraseña incorrectos.';
    }

    // Cerrar la conexión
    $conexion->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ingreso</title>
    <link rel="stylesheet" type="text/css" href="./styles/stile.css">
    <link rel="stylesheet" media="all" href="./styles/fon.css" />
</head>
<body>

<div class="hero"> 
  <div class="cube"></div>
  <div class="cube"></div>
  <div class="cube"></div>
  <div class="cube"></div>
  <div class="cube"></div>
  <div class="cube"></div>

  <img class="wave" src="img/image.png">
    <div class="container">
        <div class="img">
            <img src="" >
        </div>

        <div class="login-content">
            <?php if (isset($error)) : ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            
            <form method="post" action="login.php">
                <img src="img/avatar.svg">
                <h2 class="title">Bienvenidos</h2>
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="div">
                        <label for="nombre_usuario">Nombre de Usuario:</label>
                        <input type="text" name="nombre_usuario" required>
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i"> 
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <label for="contrasena">Contraseña:</label>
                        <input type="password" name="contrasena" required>
                    </div>
                </div>
                <input type="submit" class="btn" value="Ingresar">
            </form>
        </div>
    </div>
</div>
    
</body>
</html>
