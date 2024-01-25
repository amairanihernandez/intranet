<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['idusuario'])) {
    header('Location: login.php');
    exit();
}

// Obtener información del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'];
$perfil_usuario = $_SESSION['perfil'];

$user_id = $_SESSION['idusuario'];
$servername = "localhost";
$username = "root";
$password = "";
$database = "orden_compra1";

$conn = new mysqli($servername, $username, $password, $database);

// Mensaje para mostrar si se actualizó el perfil
$mensaje_actualizacion = "";

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $new_nombre = $conn->real_escape_string($_POST['nombre']);
    $new_passwd = password_hash($_POST['passwd'], PASSWORD_DEFAULT);

    // Actualizar datos en la base de datos (nombre, passwd y ultima_actualizacion)
    $query = "UPDATE users SET nombre = '$new_nombre', passwd = '$new_passwd', ultima_actualizacion = NOW() WHERE idusuario = $user_id";
    $result = $conn->query($query);

    if ($result) {
        $mensaje_actualizacion = "Perfil actualizado correctamente.";

        // Procesar la carga de nueva imagen solo si se seleccionó una
        if ($_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
            $nombre_temporal = $_FILES['imagen']['tmp_name'];
            $nombre_destino = './fotos/' . $_FILES['imagen']['name'];

            // Validar que el archivo sea una imagen (puedes ajustar esto según tus necesidades)
            $es_imagen = getimagesize($nombre_temporal);
            if ($es_imagen !== false) {
                // Eliminar la imagen anterior si existe
                $query_select_imagen = "SELECT imagen FROM users WHERE idusuario = $user_id";
                $result_select_imagen = $conn->query($query_select_imagen);

                if ($result_select_imagen && $row = $result_select_imagen->fetch_assoc()) {
                    $imagen_anterior = $row['imagen'];
                    if (!empty($imagen_anterior)) {
                        unlink($imagen_anterior); // Eliminar la imagen anterior del servidor
                    }
                }

                // Mover el nuevo archivo al destino final
                move_uploaded_file($nombre_temporal, $nombre_destino);

                // Actualizar la ruta de la nueva imagen en la base de datos
                $query = "UPDATE users SET imagen = '$nombre_destino' WHERE idusuario = $user_id";
                $conn->query($query);
            } else {
                $mensaje_actualizacion = "Error: El archivo no es una imagen válida.";
            }
        }
    } else {
        $mensaje_actualizacion = "Error al actualizar el perfil.";
    }
}

// Obtener datos actuales del usuario desde la base de datos
$query = "SELECT nombre, passwd, numero_nomina, fecha_ingreso, imagen FROM users WHERE idusuario = $user_id";
$result = $conn->query($query);

if ($result) {
    $user_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        p {
            color: #666;
            margin-bottom: 15px;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 70px;
            border-radius: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: 'Times New Roman', serif;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 20px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        .info-container {
            overflow: hidden;
        }

        .info-container img {
            float: left;
            margin-right: 20px;
            border-radius: 50%;
        }
    </style>

    <script>
        function validarContrasena() {
            var contrasena = document.getElementById("passwd").value;

            // Expresiones regulares para verificar la complejidad de la contraseña
            var tieneMayuscula = /[A-Z]/.test(contrasena);
            var tieneMinuscula = /[a-z]/.test(contrasena);
            var tieneNumero = /\d/.test(contrasena);
            var tieneCaracterEspecial = /[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(contrasena);

            // Verificar si la contraseña cumple con los criterios
            if (tieneMayuscula && tieneMinuscula && tieneNumero && tieneCaracterEspecial) {
                return true;
            } else {
                alert("La contraseña debe contener al menos 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial.");
                return false;
            }
        }
    </script>
</head>

<?php if ($perfil_usuario === "3") : ?>
    <?php include "header.php"; ?>
    <!-- Contenido específico para administradores -->

<?php else : ?>
    <?php include "header.php"; ?>
    <!-- Contenido específico para usuarios normales -->
<?php endif; ?>

<body>

    <div class="info-container">

        <form method="post" action="editar_perfil.php" enctype="multipart/form-data">
            <h2>Datos del Perfil</h2>

            <div class="info-container">
                <?php if (!empty($user_data['imagen'])) : ?>
                    <img src="<?php echo $user_data['imagen']; ?>" width="120" height="120" alt="Imagen de perfil" style="border-radius: 50%;">
                <?php endif; ?>

                <p>Nombre: <?php echo $user_data['nombre']; ?></p>
                <p>Número de Nómina: <?php echo $user_data['numero_nomina']; ?></p>
                <p>Fecha de Ingreso: <?php echo $user_data['fecha_ingreso']; ?></p>
            </div>

            <?php if (!empty($mensaje_actualizacion)) : ?>
                <p style="color: <?php echo $result ? 'green' : 'red'; ?>"><?php echo $mensaje_actualizacion; ?></p>
            <?php endif; ?>

            <h2>Editar Perfil</h2>
            <input type="hidden" name="idusuario" value="<?php echo $user_id; ?>">

            <label for="nombre">Nuevo Nombre:</label>
            <input type="text" name="nombre" value="<?php echo $user_data['nombre']; ?>" required>

            <label for="passwd">Nueva Contraseña o contraseña actual:</label>
            <input type="password" name="passwd" id="passwd" value="" placeholder="La contraseña debe contener al menos 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial" required>

            <!-- Mostrar la imagen actual y permitir cargar una nueva -->
            <label for="imagen">Imagen Actual:</label>
            <?php if (!empty($user_data['imagen'])) : ?>
                <img src="<?php echo $user_data['imagen']; ?>" width="120" height="120" alt="Imagen de perfil actual" style="border-radius: 50%;">
            <?php endif; ?>
            <br>
            <label for="imagen" style="font-family: 'Times New Roman', serif;">Seleccionar Nueva Imagen:</label>
            <input type="file" name="imagen" style="font-family: 'Times New Roman', serif;">

            <input type="submit" value="Guardar Cambios" onclick="return validarContrasena();" style="font-family: 'Times New Roman', serif; color:black;">
        </form>
    </div>

</body>

</html>

<?php
} else {
    echo "Error al obtener datos del usuario.";
}

// Cierra la conexión a la base de datos al finalizar
$conn->close();
?>
