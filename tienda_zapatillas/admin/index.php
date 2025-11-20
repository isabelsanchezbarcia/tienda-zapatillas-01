<?php
session_start();
include '../config.php';

// verificamos si el usuario ha iniciado sesión y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del administrador</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

<?php include '../header.php'; ?>

<div class="panel-admin">

    <h2>Panel del administrador</h2>
    <p>Bienvenida, <?php echo $_SESSION['usuario_nombre']; ?>.</p>

    <a href="añadir_producto.php" class="boton-admin">Añadir producto</a>
    <a href="ver_productos.php" class="boton-admin">Ver productos</a>
    <a href="ver_pedidos.php" class="boton-admin">Ver pedidos</a>
    <a href="../logout.php" class="boton-admin">Cerrar sesión</a>

</div>

</body>
</html>