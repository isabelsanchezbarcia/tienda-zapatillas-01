<?php
session_start(); 
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {

        $usuario = $resultado->fetch_assoc();

        // verificar contraseña
        if (password_verify($password, $usuario['password'])) {

            // guardar en sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];

            // redirigir según el tipo de usuario
            if ($usuario['tipo_usuario'] === 'admin') {
                header("Location: admin/index.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }

        } else {
            echo "<p>Contraseña incorrecta.</p>";
        }

    } else {
        echo "<p>No existe ningún usuario con ese correo.</p>";
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
<div class="form-contenedor">
    <h2>Inicio de sesión</h2>
    <form method="POST" action="">
            <label>Email:</label><br>
            <input type="email" name="email" required><br><br>
            
            <label>Contraseña:</label><br>
            <input type="password" name="password" required><br><br>
            
            <button type="submit">Iniciar sesión</button>
    </form>

</div>

</body>
</html>