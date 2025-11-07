<?php
session_start();
include 'config.php';

// Obtenemos todos los productos de la base de datos
$sql = "SELECT * FROM productos";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilos.css">
    <title>tienda 01</title>
</head>

<body>

<?php include 'header.php'; ?>

<div class="productos">
<?php
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        echo "<div class='producto'>";
        echo "<img src='img/" . htmlspecialchars($fila['imagen']) . "' alt='" . htmlspecialchars($fila['nombre']) . "'>";
        echo "<h3>" . htmlspecialchars($fila['nombre']) . "</h3>";
        echo "<p>" . htmlspecialchars($fila['descripcion']) . "</p>";
        echo "<p class='precio'>" . $fila['precio'] . " €</p>";
        echo "<a class='boton' href='añadir_carrito.php?id=" . $fila['id'] . "'>Añadir al carrito</a>";
        echo "</div>";
    }
} else {
    echo "<p>No hay productos disponibles en este momento.</p>";
}
?>
</div>

</body>
</html>
