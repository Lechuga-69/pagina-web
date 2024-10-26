<?php
session_start();

// Conexión a la base de datos
$servername = "sql207.infinityfree.com";
$username = "if0_37378666";
$password = "perrocafe123";
$dbname = "if0_37378666_REG_IS";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Manejar búsqueda
$searchTerm = "";
if (isset($_GET['buscar'])) {
    $searchTerm = $conn->real_escape_string($_GET['buscar']);
}

// Manejar agregar/editar mensaje
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = $conn->real_escape_string($_POST['mensaje']);
    $imagen = "";
    $id = isset($_POST['id']) ? $_POST['id'] : "";

    // Subir imagen
    if (!empty($_FILES['imagen']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
        $imagen = $target_file;
    }

    if (!empty($id)) {
        // Editar mensaje existente
        $sql = "UPDATE mensajes SET mensaje='$mensaje', imagen='$imagen' WHERE id='$id' AND usuario='$usuario'";
    } else {
        // Insertar mensaje nuevo
        $sql = "INSERT INTO mensajes (usuario, mensaje, imagen) VALUES ('$usuario', '$mensaje', '$imagen')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: chat.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// Manejar eliminar mensaje
if (isset($_GET['eliminar'])) {
    $idEliminar = $conn->real_escape_string($_GET['eliminar']);
    $sqlEliminar = "DELETE FROM mensajes WHERE id='$idEliminar' AND usuario='$usuario'";
    $conn->query($sqlEliminar);
    header("Location: chat.php");
    exit;
}

// Obtener todos los mensajes (con búsqueda si es necesario)
$sql = "SELECT * FROM mensajes WHERE mensaje LIKE '%$searchTerm%' ORDER BY fecha ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="DPP.css">
    <link rel="stylesheet" href="DEC.css">
    <link rel="stylesheet" href="BOT.css">
</head>
<body class="bodyreg">

<header>
    <h1>Conoce a otros amantes de los perros</h1>
</header>

<div class="cont-bot">
    <button onclick="location.href='index.html'">Volver a la página principal</button>
    <button onclick="location.href='formulario.html'">¿Buscas algún perro en específico?</button>
</div>

<h2>Mensajes anteriores</h2>

<form action="chat.php" method="get">
    <input type="text" name="buscar" placeholder="Buscar mensaje..." value="<?php echo htmlspecialchars($searchTerm); ?>">
    <button type="submit">Buscar</button>
</form>

<div class="chat">
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div>";
            echo "<p><strong>" . htmlspecialchars($row['usuario']) . "</strong> (" . $row['fecha'] . "): " . htmlspecialchars($row['mensaje']) . "</p>";
            if (!empty($row['imagen'])) {
                echo "<img src='" . $row['imagen'] . "' alt='Imagen' style='max-width: 200px;'><br>";
            }
            if ($row['usuario'] == $usuario) {
                echo "<a href='chat.php?editar=" . $row['id'] . "'>Editar</a> | ";
                echo "<a href='chat.php?eliminar=" . $row['id'] . "' onclick=\"return confirm('¿Estás seguro?');\">Eliminar</a>";
            }
            echo "</div><hr>";
        }
    } else {
        echo "No hay mensajes aún.";
    }
    ?>
</div>

<?php
// Si se va a editar un mensaje
$mensajeAEditar = "";
$idAEditar = "";
if (isset($_GET['editar'])) {
    $idAEditar = $conn->real_escape_string($_GET['editar']);
    $sqlEditar = "SELECT * FROM mensajes WHERE id='$idAEditar' AND usuario='$usuario'";
    $resultEditar = $conn->query($sqlEditar);
    if ($resultEditar->num_rows > 0) {
        $rowEditar = $resultEditar->fetch_assoc();
        $mensajeAEditar = $rowEditar['mensaje'];
    }
}
?>

<form action="chat.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($idAEditar); ?>">
    <textarea name="mensaje" placeholder="Escribe tu mensaje..." required><?php echo htmlspecialchars($mensajeAEditar); ?></textarea><br>
    <input type="file" name="imagen"><br>
    <button type="submit">Enviar</button>
</form>

</body>
</html>

<?php
$conn->close();
?>
