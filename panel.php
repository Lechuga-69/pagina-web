<?php
session_start(); // Iniciar la sesión para manejar mensajes

// Configurar la conexión a la base de datos
$servername = "sql207.infinityfree.com";
$username = "if0_37378666";
$password = "perrocafe123";
$dbname = "if0_37378666_REG_IS";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variable para almacenar los datos del perro a editar
$edit_perro = null;

// Manejo de acciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Editar: cargar los datos del perro en el formulario de edición
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $sql = "SELECT * FROM perros WHERE id = $id";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $edit_perro = $result->fetch_assoc(); // Cargar datos del perro a editar
        }
    }

    // Actualizar
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = intval($_POST['id']);
        $raza = $conn->real_escape_string($_POST['raz']);
        $color_pelaje = $conn->real_escape_string($_POST['cdp']);
        $sexo = $conn->real_escape_string($_POST['sexo']);
        $tamaño = $conn->real_escape_string($_POST['tm']);

        $sql = "UPDATE perros SET raz='$raza', cdp='$color_pelaje', sexo='$sexo', tm='$tamaño' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Perro actualizado exitosamente.";
        } else {
            $_SESSION['message'] = "Error al actualizar perro: " . $conn->error;
        }
        
        header("Location: panel.php");
        exit();
    }

    // Crear
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $raza = $conn->real_escape_string($_POST['raz']);
        $color_pelaje = $conn->real_escape_string($_POST['cdp']);
        $sexo = $conn->real_escape_string($_POST['sexo']);
        $tamaño = $conn->real_escape_string($_POST['tm']);

        // Insertar el nuevo registro
        $sql = "INSERT INTO perros (raz, cdp, sexo, tm) VALUES ('$raza', '$color_pelaje', '$sexo', '$tamaño')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Registro añadido exitosamente.";
        } else {
            $_SESSION['message'] = "Error al añadir el registro: " . $conn->error;
        }
        
        header("Location: panel.php"); // Redirige a la misma página para evitar reenvíos del formulario
        exit();
    }

    // Borrar
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM perros WHERE id = $id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Perro borrado exitosamente.";
        } else {
            $_SESSION['message'] = "Error al borrar perro: " . $conn->error;
        }
        
        header("Location: panel.php");
        exit();
    }
}

// Consultar los perros
$perros = [];
$sql = "SELECT * FROM perros";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $perros[] = $row;
    }
} else {
    $_SESSION['message'] = "Error al cargar los perros: " . $conn->error;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="DPP.css">
    <link rel="stylesheet" href="DEC.css">
    <link rel="stylesheet" href="BOT.css">
</head>
<body class="bodyreg">
    <h2>Panel de Administración de Perros</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <!-- Formulario para agregar perros -->
    <form method="post" action="">
        <fieldset>
            <legend><?php echo $edit_perro ? "Editar Perro" : "Agregar Perro"; ?></legend>
            <input type="hidden" name="id" value="<?php echo $edit_perro ? $edit_perro['id'] : ''; ?>">
            <label for="raz">Raza:</label>
            <input type="text" id="raz" name="raz" value="<?php echo $edit_perro ? $edit_perro['raz'] : ''; ?>" required>
            <label for="cdp">Color de Pelaje:</label>
            <input type="text" id="cdp" name="cdp" value="<?php echo $edit_perro ? $edit_perro['cdp'] : ''; ?>" required>
            <label for="sexo">Sexo:</label>
            <input type="text" id="sexo" name="sexo" value="<?php echo $edit_perro ? $edit_perro['sexo'] : ''; ?>" required>
            <label for="tm">Tamaño (CM):</label>
            <input type="number" id="tm" name="tm" value="<?php echo $edit_perro ? $edit_perro['tm'] : ''; ?>" required>
            <input type="hidden" name="action" value="<?php echo $edit_perro ? 'update' : 'create'; ?>">
            <button type="submit"><?php echo $edit_perro ? "Actualizar Perro" : "Agregar Perro"; ?></button>
        </fieldset>
    </form>

    <div style="background-color: rgb(254, 249, 171);">
    <!-- Tabla de perros -->
    <h3>Lista de Perros</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Raza</th>
            <th>Color de Pelaje</th>
            <th>Sexo</th>
            <th>Tamaño (CM)</th>
            <th>Acciones</th>
        </tr>
        <?php if (!empty($perros)): ?>
            <?php foreach ($perros as $perro): ?>
                <tr>
                    <td><?php echo $perro['id']; ?></td>
                    <td><?php echo $perro['raz']; ?></td>
                    <td><?php echo $perro['cdp']; ?></td>
                    <td><?php echo $perro['sexo']; ?></td>
                    <td><?php echo $perro['tm']; ?></td>
                    <td>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $perro['id']; ?>">
                            <input type="hidden" name="action" value="edit">
                            <button class="bot" type="submit">Editar</button>
                        </form>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $perro['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button class="bot" type="submit" onclick="return confirm('¿Estás seguro de que deseas borrar este perro?');">Borrar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No se encontraron perros</td>
            </tr>
        <?php endif; ?>
    </table>
    </div>
    <div class="cont-bot" style="margin: 45px auto;">
    <button class="bot" onclick="location.href='index.html'" style="width: 100; height: 75px;">Volver a la página principal?</button>
    <button class="bot" onclick="location.href='AC.php'" style="width: 100; height: 75px;">Administrar chat</button>
    </div>
</body>
</html>
