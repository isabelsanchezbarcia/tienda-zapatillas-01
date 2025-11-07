<?php
session_start();
include '../config.php';

// Comprobamos que el usuario es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}

// Si se recibe un id por la URL, eliminamos ese producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $sql = "DELETE FROM productos WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green; text-align:center;'>Producto eliminado correctamente.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Error al eliminar: " . $conn->error . "</p>";
    }
}

// Obtenemos todos los productos
$sql = "SELECT * FROM productos";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar productos</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

<?php include '../header.php'; ?>

<div class="panel-admin">

    <h2>Lista de productos</h2>
    <a href="index.php" class="boton-admin">Volver al panel</a>

    <?php
    if ($resultado->num_rows > 0) {

        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio (€)</th>
                <th>Imagen</th>
                <th>Acciones</th>
              </tr>";

        while ($fila = $resultado->fetch_assoc()) {

            echo "<tr>";
            echo "<td>" . $fila['id'] . "</td>";
            echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($fila['descripcion']) . "</td>";
            echo "<td>" . $fila['precio'] . "</td>";
            echo "<td><img src='../img/" . htmlspecialchars($fila['imagen']) . "' width='80'></td>";

            echo "<td class='tabla-acciones'>
                    <a class='btn-crud btn-editar' href='editar_producto.php?id=" . $fila['id'] . "'>Editar</a>
                    <a class='btn-crud btn-eliminar'
                       href='ver_productos.php?eliminar=" . $fila['id'] . "'
                       onclick=\"return confirm('¿Seguro que quieres eliminar este producto?');\">
                       Eliminar
                    </a>
                  </td>";

            echo "</tr>";
        }

        echo "</table>";

    } else {
        echo "<p>No hay productos añadidos todavía.</p>";
    }
    ?>

</div>

</body>
</html>
