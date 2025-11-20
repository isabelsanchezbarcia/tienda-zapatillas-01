<?php
session_start();
include 'config.php';

$errores = [];
$exito = "";

/* si ya está logeado fuera*/
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* recoger y limpiar datos */
    $nombre    = trim($_POST['nombre']   ?? '');
    $email     = trim($_POST['email']    ?? '');
    $password  = $_POST['password']      ?? '';
    $password2 = $_POST['password2']     ?? '';

    /* validaciones */

    // nombre
    if ($nombre === '') {
        $errores[] = "El nombre es obligatorio.";
    } elseif (mb_strlen($nombre) < 2) {
        $errores[] = "El nombre es demasiado corto.";
    }

    // email
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Introduce un email válido.";
    }

    // contraseña
    if ($password === '') {
        $errores[] = "La contraseña es obligatoria.";
    } elseif (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // confirmación
    if ($password2 === '') {
        $errores[] = "Debes repetir la contraseña.";
    } elseif ($password !== $password2) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    /* comprobar email duplicado */
    if (empty($errores)) {

        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if ($stmt === false) {
            $errores[] = "Error interno. Intenta más tarde.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows > 0) {
                $errores[] = "Ya existe una cuenta con ese email.";
            }
            $stmt->close();
        }
    }

    /* registrar usuario */
    if (empty($errores)) {

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $tipo_usuario = "cliente";

        $stmt = $conn->prepare(
            "INSERT INTO usuarios (nombre, email, password, tipo_usuario)
             VALUES (?, ?, ?, ?)"
        );

        if ($stmt === false) {
            $errores[] = "Error interno. Inténtalo de nuevo más tarde.";
        } else {
            $stmt->bind_param("ssss", $nombre, $email, $password_hash, $tipo_usuario);

            if ($stmt->execute()) {
                $exito = "Registro completado correctamente. Ya puedes iniciar sesión.";
                $_POST = []; // limpiar formulario
            } else {
                $errores[] = "No se ha podido completar el registro.";
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
    <title>Registrarse</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="form-contenedor">
    <h2>Crear cuenta</h2>

    <!-- mensaje de éxito -->
    <?php if (!empty($exito)): ?>
        <div style="color: green; margin-bottom: 12px; font-weight: bold;">
            <?php echo htmlspecialchars($exito); ?>
        </div>
    <?php endif; ?>

    <!-- errores -->
    <?php if (!empty($errores)): ?>
        <div style="color: red; margin-bottom: 12px;">
            <?php foreach ($errores as $e): ?>
                <p><?php echo htmlspecialchars($e); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- formulario -->
    <form method="POST" action="register.php">

        <label>Nombre:</label>
        <input type="text" name="nombre" required
               value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">

        <label>Email:</label>
        <input type="email" name="email" required
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <label>Repetir contraseña:</label>
        <input type="password" name="password2" required>

        <button type="submit">Registrarse</button>
    </form>
</div>

</body>
</html>
