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

// Manejar el envío de mensaje
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensaje = $conn->real_escape_string($_POST['mensaje']);
    $imagen = "";

    // Subir imagen
    if (!empty($_FILES['imagen']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
        $imagen = $target_file;
    }

    // Insertar mensaje en la base de datos
    $sql = "INSERT INTO mensajes (usuario, mensaje, imagen) VALUES ('$usuario', '$mensaje', '$imagen')";
    if ($conn->query($sql) === TRUE) {
    } else {
        echo "Error: " . $conn->error;
    }
}

// Obtener todos los mensajes
$sql = "SELECT * FROM mensajes ORDER BY fecha ASC";
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
<style>
    header {
    text-align: center;
    background-color: rgb(254, 249, 171);
    color: rgb(0, 0, 0);
    font-family: 'Fixedsys', monospace;
    font-size: 25px;
    padding: .1px 0px;
    position: relative;
    gap: 20px;
    justify-content: center;
    align-items: center;
    height:100px
}
    .sticky-button {
            position: sticky;
            top: 20px; 
            background-color: rgb(254, 249, 171);
            padding: 15px 20px;
            font-family: 'Fixedsys', monospace;
            cursor: pointer;
            font-size: 16px;
            z-index: 1000; 
        }
    .sticky-button:hover {
    background-color: #0056b3;
    transform: scale(1.05);
    color:aliceblue
    }

    .sticky-button:active {
    background-color: #022d5b; 
    transform: scale(0.95);
    }
    .chat {
        border: 1px solid black;
        color: rgb(0, 0, 0);
        font-family: 'Fixedsys';
        text-align: center;
        background-color: rgb(255, 255, 255);;
        max-width: 700px; /* Ancho máximo de la caja del mensaje */
        padding: 10px; /* Espaciado interno */
        border: 1px solid #ccc; /* Borde */
        border-radius: 5px; /* Bordes redondeados */
        margin-bottom: 10px; /* Espacio entre mensajes */
        overflow-wrap: break-word; /* Permite que las palabras largas se dividan */
        background-color: #f9f9f9; /* Color de fondo */
    }
    textarea {
    width: 100%; /* Ancho completo del contenedor */
    max-width: 100%; /* Asegura que no supere el contenedor */
    height: 40px; /* Altura inicial */
    max-height: 150px; /* Altura máxima antes de mostrar la barra de desplazamiento */
    overflow-y: auto; /* Muestra barra de desplazamiento si es necesario */
    resize: vertical; /* Permite al usuario redimensionar verticalmente */
    border: 1px solid #ccc; /* Borde */
    border-radius: 5px; /* Bordes redondeados */
}
</style>
<header>
<h1 class="TXT-DX">Conoce a otros amantes de los perros</h1>
</header>
<div class="cont-bot sticky-button">
<button class="sticky-button" onclick="location.href='index.html'">Volver a la página principal?</button>
<button class="sticky-button" onclick="location.href='formulario.html'">¿Buscas algun perro en especifico?</button>
</div>
<body class="bodyreg">
    <h2>Mensajes anteriores</h2>
    <div class="chat">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div>";
                echo "<p><strong>" . htmlspecialchars($row['usuario']) . "</strong> (" . $row['fecha'] . "): " . htmlspecialchars($row['mensaje']) . "</p>";
                if (!empty($row['imagen'])) {
                    echo "<img src='" . $row['imagen'] . "' alt='Imagen' style='max-width: 200px;'><br>";
                }
                echo "</div><hr>";
            }
        } else {
            echo "No hay mensajes aún.";
        }
        ?>
    </div>
    <form action="chat.php" method="post" enctype="multipart/form-data">
    <fieldset style="text-align: center;  width: 250px; height: 250; border: 1px solid rgb(0, 0, 0); margin: 100px auto;">
        <textarea name="mensaje" placeholder="Escribe tu mensaje..." required></textarea><br>
        <input type="file" name="imagen"><br>
        <button class="bot" type="submit">Enviar</button>
        </fieldset>
    </form>
</body>
</html>

<?php
$conn->close();
?>
