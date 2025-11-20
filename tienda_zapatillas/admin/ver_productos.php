<?php
session_start();
include '../config.php';


// protección de admin

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}


// eliminar producto de forma SEGURA

$mensaje = "";

if (isset($_GET['eliminar'])) {

    $id = intval($_GET['eliminar']); // Sanitizar

    // prepared statement para evitar inyecciones SQL
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $mensaje = "<p style='color:green; text-align:center;'>Producto eliminado correctamente.</p>";
    } else {
        $mensaje = "<p style='color:red; text-align:center;'>Error al eliminar: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}


// obtener productos de forma segura

$resultado = $conn->query("SELECT * FROM productos");

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

    <!-- mostrar mensaje de eliminación -->
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <?php if ($resultado && $resultado->num_rows > 0): ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio (€)</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>

            <?php while ($fila = $resultado->fetch_assoc()): ?>

                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                    <td><?php echo number_format($fila['precio'], 2, ',', '.'); ?></td>

                    <td>
                        <img src="../img/<?php echo htmlspecialchars($fila['imagen']); ?>" 
                             width="80" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
                    </td>

                    <td class="tabla-acciones">
                        <a class="btn-crud btn-editar"
                           href="editar_producto.php?id=<?php echo $fila['id']; ?>">
                           Editar
                        </a>

                        <a class="btn-crud btn-eliminar"
                           href="ver_productos.php?eliminar=<?php echo $fila['id']; ?>"
                           onclick="return confirm('¿Seguro que quieres eliminar este producto?');">
                           Eliminar
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>

        </table>

    <?php else: ?>

        <p>No hay productos añadidos todavía.</p>

    <?php endif; ?>

</div>

</body>
</html>
