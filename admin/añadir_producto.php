<?php
session_start();
include '../config.php';

// C¡comprobar si el usuario es admin o no
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}

// procesar el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // imagen
    $imagen = $_FILES['imagen']['name'];
    $ruta_temp = $_FILES['imagen']['tmp_name'];

    if ($imagen != "") {
        move_uploaded_file($ruta_temp, "../img/" . $imagen);
    }

    $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen)
            VALUES ('$nombre', '$descripcion', '$precio', '$imagen')";

    if ($conn->query($sql)) {
        $mensaje = "<p style='color:green; text-align:center; font-weight:bold;'>✅ Producto añadido correctamente</p>";
    } else {
        $mensaje = "<p style='color:red; text-align:center;'>❌ Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

<?php include '../header.php'; ?>

<div class="panel-admin">

    <h2>Añadir nuevo producto</h2>
    <a href="index.php" class="boton-admin">Volver al panel</a>

    <?php if (isset($mensaje)) echo $mensaje; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="form-contenedor" style="margin-top:30px;">

        <label>Nombre del producto:</label>
        <input type="text" name="nombre" required>

        <label>Descripción:</label>
        <textarea name="descripcion" class="textarea-producto" rows="5" required></textarea>

        <label>Precio (€):</label>
        <input type="number" name="precio" step="0.01" required>

        <label>Imagen:</label>
        <input type="file" name="imagen" required>

        <button type="submit">Añadir producto</button>
    </form>

</div>

</body>
</html>