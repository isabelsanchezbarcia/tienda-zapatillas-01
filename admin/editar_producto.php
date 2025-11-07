<?php
session_start();
include '../config.php';

// Comprobar si el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}

// Comprobar ID
if (!isset($_GET['id'])) {
    echo "<p>Error: producto no especificado.</p>";
    exit();
}

$id = $_GET['id'];

// Obtener datos del producto
$sql = "SELECT * FROM productos WHERE id = $id";
$resultado = $conn->query($sql);

if ($resultado->num_rows == 0) {
    echo "<p>Producto no encontrado.</p>";
    exit();
}

$producto = $resultado->fetch_assoc();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Si NO se sube imagen nueva → mantener la antigua
    if ($_FILES['imagen']['name'] == "") {
        $imagen = $producto['imagen'];
    } else {
        $imagen = $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], "../img/" . $imagen);
    }

    $sql = "UPDATE productos SET 
                nombre='$nombre',
                descripcion='$descripcion',
                precio='$precio',
                imagen='$imagen'
            WHERE id=$id";

    if ($conn->query($sql)) {
        $mensaje = "<p style='color:green; text-align:center; font-weight:bold;'>✅ Cambios guardados correctamente</p>";
        // Actualizar datos en pantalla sin recargar
        $producto['nombre'] = $nombre;
        $producto['descripcion'] = $descripcion;
        $producto['precio'] = $precio;
        $producto['imagen'] = $imagen;
    } else {
        $mensaje = "<p style='color:red; text-align:center;'>❌ Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar producto</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

<?php include '../header.php'; ?>

<div class="panel-admin">

    <h2>Editar producto</h2>

    <a href="ver_productos.php" class="boton-admin">Volver</a>

    <?php if (isset($mensaje)) echo $mensaje; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="form-contenedor" style="margin-top:25px;">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

        <label>Descripción:</label>
        <textarea name="descripcion" class="textarea-producto" rows="5" required><?php 
            echo htmlspecialchars($producto['descripcion']); 
        ?></textarea>

        <label>Precio (€):</label>
        <input type="number" name="precio" step="0.01" value="<?php echo $producto['precio']; ?>" required>

        <label>Imagen actual:</label><br>
        <img src="../img/<?php echo $producto['imagen']; ?>" width="120" style="border-radius:6px; margin-bottom:12px;"><br><br>

        <label>Subir nueva imagen (opcional):</label>
        <input type="file" name="imagen">

        <button type="submit">Guardar cambios</button>
    </form>

</div>

</body>
</html>
