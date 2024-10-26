<?php
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

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $nombre = $conn->real_escape_string($_POST['nom']);
    $correo = $conn->real_escape_string($_POST['corr']);
    $usuario = $conn->real_escape_string($_POST['usr']);
    $contraseña = password_hash($conn->real_escape_string($_POST['contra']), PASSWORD_DEFAULT);

    // Verificar si el correo o el usuario ya están registrados
    $sql_check = "SELECT * FROM usuarios WHERE corr = '$correo' OR usr = '$usuario'";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        echo "El correo o el usuario ya están registrados. Intente con otros.";
    } else {
        // Insertar los datos en la base de datos
        $sql = "INSERT INTO usuarios (nom, corr, usr, contra) VALUES ('$nombre', '$correo', '$usuario', '$contraseña')";

        if ($conn->query($sql) === TRUE) {
            header("refresh:.2; url=index.html");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Cerrar la conexión
$conn->close();
?>
