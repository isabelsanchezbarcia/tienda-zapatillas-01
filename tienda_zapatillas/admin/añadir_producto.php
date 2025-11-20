<?php
session_start();
include '../config.php';


// comprobar que es administrador

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}

$mensaje = "";


// procesar formulario seguro

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // sanitizar entradas
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    // validación de campos obligatorios
    if ($nombre === "" || $descripcion === "" || $precio <= 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios.</p>";
    } else {

        
        // procesar la imagen
       
        $imagen = "";
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!empty($_FILES['imagen']['name'])) {

            $nombreArchivo = $_FILES['imagen']['name'];
            $tmp = $_FILES['imagen']['tmp_name'];
            $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            if (!in_array($ext, $permitidas)) {
                $mensaje = "<p style='color:red; text-align:center;'>Formato de imagen no permitido.</p>";
            } else {
                // crear nombre único para evitar sobrescritura
                $imagen = uniqid("img_", true) . "." . $ext;
                move_uploaded_file($tmp, "../img/" . $imagen);
            }
        }

         
        // insert seguro con bind
        
        if ($imagen !== "") {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $imagen);

            if ($stmt->execute()) {
                $mensaje = "<p style='color:green; text-align:center; font-weight:bold;'>Producto añadido correctamente</p>";
            } else {
                $mensaje = "<p style='color:red; text-align:center;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
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
    <title>Añadir producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

<?php include '../header.php'; ?>

<div class="panel-admin">

    <h2>Añadir nuevo producto</h2>
    <a href="index.php" class="boton-admin">Volver al panel</a>

    <?php if (!empty($mensaje)) echo $mensaje; ?>

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
