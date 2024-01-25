<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "orden_compra1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID de la orden de compra desde el formulario
$orden_id = $_POST['orden_id'];

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verificar si se han marcado opciones específicas
    if (isset($_POST['autorizado'])) {

        // Obtener las opciones marcadas
        $opciones_autorizadas = $_POST['autorizado'];
        $nuevo_estado = "Revision";

        // Verificar y actualizar el estado solo si ambas opciones están presentes
        if (array_key_exists('0', $opciones_autorizadas)) {
            $codigo_usuario = $_POST['codigo_usuario'][1];
            if (verificarCodigoUsuario($codigo_usuario, 1)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Estado='$nuevo_estado', Autorizado_1='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Código de Usuario 1 incorrecto. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('1', $opciones_autorizadas)) {
            $codigo_usuario = $_POST['codigo_usuario'][1];
            if (verificarCodigoUsuario($codigo_usuario, 1)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Autorizado_1='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Código de Usuario 1 incorrecto. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('2', $opciones_autorizadas)) {
            $codigo_usuario = $_POST['codigo_usuario'][2];
            if (verificarCodigoUsuario($codigo_usuario, 2)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Autorizado_2='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Código de Usuario 2 incorrecto. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('1', $opciones_autorizadas) && array_key_exists('2', $opciones_autorizadas)) {
            $codigo_usuario_1 = $_POST['codigo_usuario'][1];
            $codigo_usuario_2 = $_POST['codigo_usuario'][2];
            if (verificarCodigoUsuario($codigo_usuario_1, 1) && verificarCodigoUsuario($codigo_usuario_2, 2)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Estado='$nuevo_estado', Autorizado_1='$nuevo_estado', Autorizado_2='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Códigos de Usuario incorrectos. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('3', $opciones_autorizadas)) {
            $codigo_usuario = $_POST['codigo_usuario'][1];
            if (verificarCodigoUsuario($codigo_usuario, 1)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Autorizado_1='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Código de Usuario 1 incorrecto. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('4', $opciones_autorizadas)) {
            $codigo_usuario = $_POST['codigo_usuario'][2];
            if (verificarCodigoUsuario($codigo_usuario, 2)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Autorizado_2='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Código de Usuario 2 incorrecto. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('5', $opciones_autorizadas)) {
            $codigo_usuario = $_POST['codigo_usuario'][3];
            if (verificarCodigoUsuario($codigo_usuario, 3)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Estado='$nuevo_estado', Autorizado_3='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Código de Usuario 3 incorrecto. Estado no actualizado.";
                exit();
            }
        }

        if (array_key_exists('3', $opciones_autorizadas) && array_key_exists('4', $opciones_autorizadas) && array_key_exists('5', $opciones_autorizadas)) {
            $codigo_usuario_1 = $_POST['codigo_usuario'][1];
            $codigo_usuario_2 = $_POST['codigo_usuario'][2];
            $codigo_usuario_3 = $_POST['codigo_usuario'][3];
            if (verificarCodigoUsuario($codigo_usuario_1, 1) && verificarCodigoUsuario($codigo_usuario_2, 2) && verificarCodigoUsuario($codigo_usuario_3, 3)) {
                $nuevo_estado = "Autorizado";
                $sql_update = "UPDATE orden_compra SET Estado='$nuevo_estado', Autorizado_1='$nuevo_estado', Autorizado_2='$nuevo_estado', Autorizado_3='$nuevo_estado' WHERE id=$orden_id";
            } else {
                echo "Códigos de Usuario incorrectos. Estado no actualizado.";
                exit();
            }
        }

        // Actualizar el estado en la base de datos
        if ($conn->query($sql_update) === TRUE) {
            echo "Estado actualizado correctamente";
        } else {
            echo "Error al actualizar el estado: " . $conn->error;
        }
    } else {
        echo "No se han marcado opciones para cambiar el estado.";
    }
}

// Función para verificar el código de usuario
function verificarCodigoUsuario($codigo, $usuario_id)
{
    global $conn, $orden_id;

    $sql = "SELECT codigo FROM usuarios WHERE id=$usuario_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $codigo_correcto = $row['codigo'];

        // Verificar si el código proporcionado coincide con el código almacenado en la base de datos
        return ($codigo == $codigo_correcto);
    } else {
        return false;
    }
}

// Cerrar la conexión
$conn->close();
?>

        <button onclick="window.location.href='autorizacion.php'">
            Listado de solicitudes
        </button>