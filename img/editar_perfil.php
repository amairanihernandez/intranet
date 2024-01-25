<?php
// Conexión a la base de datos (reemplaza con tus propios datos)
$servername = "tu_servidor";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verifica si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos del formulario
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $contrasena = $_POST["contrasena"];

    // Actualiza los datos en la base de datos
    $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', contrasena='$contrasena' WHERE id='ID_DEL_USUARIO_A_ACTUALIZAR'";

    if ($conn->query($sql) === TRUE) {
        echo "Perfil actualizado correctamente";
    } else {
        echo "Error al actualizar el perfil: " . $conn->error;
    }
}

// Obtiene los datos actuales del usuario
$idUsuario = $_GET["id"]; // Reemplaza con el método correcto para obtener el ID del usuario
$sql = "SELECT * FROM usuarios WHERE id='$idUsuario'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombreActual = $row["nombre"];
    $emailActual = $row["email"];
} else {
    echo "Usuario no encontrado";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
</head>
<body>

<h2>Editar Perfil</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" value="<?php echo $nombreActual; ?>" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?php echo $emailActual; ?>" required><br>

    <label for="contrasena">Contraseña:</label>
    <input type="password" name="contrasena" required><br>

    <input type="submit" value="Actualizar Perfil">
</form>

</body>
</html>
