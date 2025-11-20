<?php
session_start();
include '../config.php';


// comprobar si el usuario es admin

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}


// comprobar ID del producto

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "<p>Error: producto no especificado.</p>";
    exit();
}

$id = intval($_GET['id']);


// obtener datos del producto

$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo "<p>Producto no encontrado.</p>";
    exit();
}

$producto = $resultado->fetch_assoc();
$stmt->close();

$mensaje = "";


// procesar formulario

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $imagen_final = $producto['imagen']; // mantener imagen si no cambia

    // validación básica
    if ($nombre === "" || $descripcion === "" || $precio <= 0) {
        $mensaje = "<p style='color:red; text-align:center;'>Todos los campos son obligatorios y deben ser válidos.</p>";
    } else {

        
        // si se sube una nueva imagen
        
        if (!empty($_FILES['imagen']['name'])) {

            $permitidas = ['jpg','jpeg','png','webp'];

            $nombreArchivo = $_FILES['imagen']['name'];
            $tmp = $_FILES['imagen']['tmp_name'];
            $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            if (!in_array($ext, $permitidas)) {
                $mensaje = "<p style='color:red; text-align:center;'>Formato de imagen no permitido.</p>";
            } else {
                // nombre único
                $imagen_final = uniqid("img_", true) . "." . $ext;
                move_uploaded_file($tmp, "../img/" . $imagen_final);
            }
        }

        
        // actualizar producto (query segura)
        
        $stmt = $conn->prepare("
            UPDATE productos 
            SET nombre = ?, descripcion = ?, precio = ?, imagen = ? 
            WHERE id = ?
        ");

        $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen_final, $id);

        if ($stmt->execute()) {
            $mensaje = "<p style='color:green; text-align:center; font-weight:bold;'>Los cambios se han guardado correctamente.</p>";

            // actualizar producto en memoria sin refrescar
            $producto['nombre'] = $nombre;
            $producto['descripcion'] = $descripcion;
            $producto['precio'] = $precio;
            $producto['imagen'] = $imagen_final;

        } else {
            $mensaje = "<p style='color:red; text-align:center;'>Error al guardar: " . htmlspecialchars($stmt->error) . "</p>";
        }

        $stmt->close();
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

    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="form-contenedor" style="margin-top:25px;">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>

        <label>Descripción:</label>
        <textarea name="descripcion" class="textarea-producto" rows="5" required><?php 
            echo htmlspecialchars($producto['descripcion']); 
        ?></textarea>

        <label>Precio (€):</label>
        <input type="number" name="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>

        <label>Imagen actual:</label><br>
        <img src="../img/<?php echo htmlspecialchars($producto['imagen']); ?>" width="120" style="border-radius:6px; margin-bottom:12px;"><br><br>

        <label>Nueva imagen (opcional):</label>
        <input type="file" name="imagen">

        <button type="submit">Guardar cambios</button>
    </form>

</div>

</body>
</html>
