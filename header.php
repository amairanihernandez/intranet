<?php
$user_id = $_SESSION['idusuario'];
$servername = "localhost";
$username = "root";
$password = "";
$database = "orden_compra1";

$conn = new mysqli($servername, $username, $password, $database);

// Obtener datos actuales del usuario desde la base de datos
$query = "SELECT nombre, passwd, numero_nomina, fecha_ingreso, imagen FROM users WHERE idusuario = $user_id";
$result = $conn->query($query);

if ($result) {
    $user_data = $result->fetch_assoc();
?>
<link rel="stylesheet" type="text/css" href="./styles/estilos.css">

<header class="green-bg">
        <div class="logo">
            <img src="img/logo.png" alt="Logo de la empresa">
        </div>
        <div></div>
        <div></div>
        <nav>
            <ul>
                <li><a href="index.php" style="font-family: 'Times New Roman', serif;">Inicio</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn" style="font-family: 'Times New Roman', serif;">Estadísticas</a>
                    <div class="dropdown-content">
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn" style="font-family: 'Times New Roman', serif;">Diario</a>
                    <div class="dropdown-content">
                        <a href="#" style="font-family: 'Times New Roman', serif;">Consultas Recomineda socio</a>
                        <a href="#" style="font-family: 'Times New Roman', serif;">ALta Saicoop</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropbtn" style="font-family: 'Times New Roman', serif;">Contabilidad</a>
                    <div class="dropdown-content">
                        <a href="orden.php" style="font-family: 'Times New Roman', serif;">Orden de compra</a>
                        <a href="autorizacion.php" style="font-family: 'Times New Roman', serif;">Listado OCR</a>
                        <a href="form.php" style="font-family: 'Times New Roman', serif;">Vacaciones</a>
                    </div>
                </li>
            </ul>
            
        </nav>
        <div></div>
        <div></div>
        <div>
        <lu>
                <li class="dropdown">
                    <a href="#" class="dropbtn">
                    <?php if (!empty($user_data['imagen'])) : ?>
                    <img src="<?php echo $user_data['imagen']; ?>" width="80" height="80" alt="Imagen de perfil" style="border-radius: 50%;">
                <?php endif; ?>
                    </a>
                    
                    <div class="dropdown-content" style="font-family: 'Times New Roman', serif;">
                        <div style="background-color: white; padding: 5px;">
                            <a href="#" style="text-decoration: none; color: black;">Usuario: <?php echo $nombre_usuario; ?></a>
                        </div>
                        <a href="editar_perfil.php">Editar Perfil</a>
                        <a href="logout.php">Cerrar Sesión</a>
                    </div>
                </li>
            </lu>
        </div>
        <div>
        </div>
    </header>
    <?php
} else {
    echo "Error al obtener datos del usuario.";
}

// Cierra la conexión a la base de datos al finalizar

?>