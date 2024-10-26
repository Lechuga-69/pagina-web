<?php
// Configurar la conexión a la base de datos
$servername = "sql207.infinityfree.com";
$username = "if0_37378666";
$password = "perrocafe123";
$dbname = "if0_37378666_REG_IS";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión es exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Iniciar sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['usr']);
    $contraseña = $_POST['contra'];

    // Verificar si el usuario existe y es administrador
    $sql = "SELECT * FROM usuarios WHERE usr = '$usuario' AND es_admin = 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtener los datos del usuario
        $row = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contraseña, $row['contra'])) {
            // Iniciar sesión exitosa, redirigir al panel de administración
            header("Location: opc.html"); // Cambia a tu página de administración
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado o no tiene acceso de administrador.";
    }
}

// Cerrar la conexión
$conn->close();
?>
