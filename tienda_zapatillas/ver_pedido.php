<?php
session_start();
include 'config.php';

// comprobar login 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = intval($_SESSION['usuario_id']);

// validad id del pedido
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "<p>Pedido no válido.</p>";
    exit;
}

$id_pedido = intval($_GET['id']);

// consulta preparada para obtener el pedido 
$stmt = $conn->prepare("
    SELECT id, fecha, total, detalles 
    FROM pedidos 
    WHERE id = ? AND id_usuario = ?
");

if (!$stmt) {
    die("Error interno al preparar la consulta.");
}

$stmt->bind_param("ii", $id_pedido, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>No tienes permiso para ver este pedido o no existe.</p>";
    exit;
}

$pedido = $res->fetch_assoc();
$stmt->close();

// decodificar JSON de forma segura 
$detalles = json_decode($pedido['detalles'], true);
if (!is_array($detalles)) {
    $detalles = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del pedido</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="panel-admin" style="max-width:850px;">

    <h2>Pedido #<?php echo $pedido['id']; ?></h2>

    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($pedido['fecha']); ?></p>
    <p><strong>Total:</strong> 
        <?php echo number_format($pedido['total'], 2, ',', '.'); ?> €
    </p>

    <h3 style="margin-top:25px;">Artículos incluidos</h3>

    <table>
        <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>

        <?php foreach ($detalles as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nombre']); ?></td>

                <td>
                    <?php echo number_format($item['precio'], 2, ',', '.'); ?> €
                </td>

                <td><?php echo intval($item['cantidad']); ?></td>

                <td>
                    <?php echo number_format($item['subtotal'], 2, ',', '.'); ?> €
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="mis_pedidos.php" class="boton-admin" style="margin-top:20px;">
        Volver
    </a>

</div>

</body>
</html>
