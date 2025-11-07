<?php
session_start();

// si no tienes nada en el carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<p>No tienes productos en el carrito.</p>";
    echo "<a href='index.php'>Volver a la tienda</a>";
    exit;
}

// para vaciar el carrito
unset($_SESSION['carrito']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Compra finalizada</title>
</head>
<body>

<?php include 'header.php'; ?>


<h1>¡Compra realizada con éxito! ✅</h1>
<p>Gracias por tu compra. Tu pedido se está procesando.</p>

<a href="index.php">Volver a la tienda</a>

</body>
</html>
