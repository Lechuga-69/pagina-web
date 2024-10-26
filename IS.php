<?php
session_start();
$servername = "sql207.infinityfree.com";
$username = "if0_37378666";
$password = "perrocafe123";
$dbname = "if0_37378666_REG_IS";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Iniciar sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['usr']);
    $contraseña = $_POST['contra'];

    // Verificar si el usuario existe
    $sql = "SELECT * FROM usuarios WHERE usr = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contraseña, $row['contra'])) {
            // Guardar nombre de usuario en la sesión
            $_SESSION['usuario'] = $row['usr'];
            
            // Redirigir al chat
            header("Location: chat.php");
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }
}

$conn->close();
?>
