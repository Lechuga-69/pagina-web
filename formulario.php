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
    $raza = $conn->real_escape_string($_POST['raz']);
    $color_pelaje = $conn->real_escape_string($_POST['cdp']);
    $sexo = $conn->real_escape_string($_POST['sexo']);
    $tamaño = $conn->real_escape_string($_POST['tm']);

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO perros (raz, cdp, sexo, tm) VALUES ('$raza', '$color_pelaje', '$sexo', '$tamaño')";

    if ($conn->query($sql) === TRUE) {
        header("refresh:2; url=DECE.html");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Cerrar la conexión
$conn->close();
?>
