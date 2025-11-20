<?php
session_start();
include '../config.php';

// comprobar si el usuario es admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    echo "<p>No tienes permiso para acceder a esta página.</p>";
    echo '<a href="../login.php">Volver al login</a>';
    exit();
}

// obtener los pedidos
$sql = "SELECT pedidos.id, pedidos.fecha, pedidos.total, pedidos.detalles, usuarios.nombre AS usuario
        FROM pedidos
        LEFT JOIN usuarios ON pedidos.id_usuario = usuarios.id
        ORDER BY pedidos.fecha DESC";

$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedidos realizados</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>

<body>

<?php include '../header.php'; ?>

<div class="panel-admin">
    <h2>Pedidos realizados</h2>

    <a href="index.php" class="boton-admin">Volver al panel</a>

    <?php
    if ($resultado && $resultado->num_rows > 0) {
        echo "<table class='tabla-admin'>";
        echo "<tr>
                <th>ID Pedido</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Total (€)</th>
                <th>Detalles</th>
              </tr>";

        while ($pedido = $resultado->fetch_assoc()) {
            $detalles = json_decode($pedido['detalles'], true);
            echo "<tr>";
            echo "<td>" . $pedido['id'] . "</td>";
            echo "<td>" . htmlspecialchars($pedido['usuario']) . "</td>";
            echo "<td>" . $pedido['fecha'] . "</td>";
            echo "<td>" . number_format($pedido['total'], 2, ',', '.') . "</td>";

            // mostrar los productos del pedido
            echo "<td>";
            if (is_array($detalles)) {
                foreach ($detalles as $item) {
                    echo htmlspecialchars($item['nombre']) . " (x" . $item['cantidad'] . ") - " . 
                         number_format($item['precio'], 2, ',', '.') . "€<br>";
                }
            } else {
                echo "Sin detalles.";
            }
            echo "</td>";

            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No hay pedidos registrados.</p>";
    }
    ?>
</div>

</body>
</html>
