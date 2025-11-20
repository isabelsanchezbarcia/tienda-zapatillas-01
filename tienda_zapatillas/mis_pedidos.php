<?php
session_start();
include 'config.php';

// comprobar login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = intval($_SESSION['usuario_id']);

// consulta preparada para obtener pedidos
$stmt = $conn->prepare("
    SELECT id, fecha, total 
    FROM pedidos 
    WHERE id_usuario = ?
    ORDER BY fecha DESC
");

if (!$stmt) {
    die("Error interno al preparar la consulta.");
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis pedidos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<?php include 'header.php'; ?>

<div class="panel-admin" style="max-width: 900px;">

    <h2>Mis pedidos</h2>

    <?php if ($resultado->num_rows === 0): ?>

        <p style="font-size:18px; text-align:center;">
            Aún no has realizado ningún pedido.
        </p>
        <div style="text-align:center; margin-top:20px;">
            <a href="index.php" class="btn-volver-tienda">Ir a la tienda</a>
        </div>

    <?php else: ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Detalles</th>
            </tr>

            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>

                    <td><?php echo htmlspecialchars($fila['fecha']); ?></td>

                    <td><?php echo number_format($fila['total'], 2, ',', '.'); ?> €</td>

                    <td>
                        <a href="ver_pedido.php?id=<?php echo $fila['id']; ?>" 
                           class="btn-crud btn-editar">
                            Ver
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </table>

    <?php endif; ?>

</div>

</body>
</html>
