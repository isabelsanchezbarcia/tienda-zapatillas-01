<?php
session_start();
include 'config.php';

$errores = [];

/* redirigir si ya está logueado */
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // recoger y limpiar datos
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    /* validaciones */
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Introduce un email válido.";
    }

    if ($password === '') {
        $errores[] = "La contraseña es obligatoria.";
    }

    /* consulta a la base de datos si hay errores */
    if (empty($errores)) {

        $stmt = $conn->prepare(
            "SELECT id, nombre, email, password, tipo_usuario 
             FROM usuarios 
             WHERE email = ? LIMIT 1"
        );

        if (!$stmt) {
            // no revelamos detalles por seguridad
            $errores[] = "Error del servidor. Inténtalo más tarde.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            /* usuario encontrado */
            if ($resultado && $resultado->num_rows === 1) {

                $usuario = $resultado->fetch_assoc();

                // verificar contraseña
                if (password_verify($password, $usuario['password'])) {

                    // aumentar seguridad de sesión
                    session_regenerate_id(true);

                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_tipo']   = $usuario['tipo_usuario'];

                    // redirección por rol
                    if ($usuario['tipo_usuario'] === 'admin') {
                        header("Location: admin/index.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit;

                } else {
                    // no especificamos si es email o contraseña → seguridad
                    $errores[] = "Email o contraseña incorrectos.";
                }

            } else {
                // usuario no encontrado → mensaje genérico
                password_verify($password, '$2y$10$abcdefghijklmnopqrstuv'); 
                $errores[] = "Email o contraseña incorrectos.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="form-contenedor">
    <h2>Iniciar sesión</h2>

    <?php if (!empty($errores)): ?>
        <div style="color: red; margin-bottom: 12px;">
            <?php foreach ($errores as $e): ?>
                <p><?php echo htmlspecialchars($e); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>Email:</label>
        <input type="email" name="email" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <button type="submit">Iniciar sesión</button>
    </form>
</div>

</body>
</html>
