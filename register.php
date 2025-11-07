<?php
// conexión con la base de datos
include 'config.php';

// al enviar el formulario (cuando pulsas el botón "Registrar")
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // para encriptar la contraseña
    $tipo_usuario = "cliente"; // todo el que se registre será cliente por defecto 

    // para que aparezca en la tabla usuarios
    $sql = "INSERT INTO usuarios (nombre, email, password, tipo_usuario)
            VALUES ('$nombre', '$email', '$password', '$tipo_usuario')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Usuario registrado correctamente.</p>";
    } else {
        echo "<p>Error al registrar el usuario: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include 'header.php'; ?>


<!-- formulario HTML -->
<div class="form-contenedor">
    <h2>Registro de usuario</h2>
    
    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Registrar</button>
</form>
</div>

</body>
</html>