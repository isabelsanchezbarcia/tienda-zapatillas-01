<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Carrito</title>
</head>
<body>

<?php include 'header.php'; ?>

<h1>Tu carrito</h1>

<a href="index.php">← Seguir comprando</a>

<?php
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<p>Tu carrito está vacío.</p>";
    exit;
}

$total_final = 0;

echo "<table>";
echo "<tr><th>Producto</th><th>Imagen</th><th>Precio</th><th>Cantidad</th><th>Total</th><th>Eliminar</th></tr>";

foreach ($_SESSION['carrito'] as $id => $cantidad) {

    $sql = "SELECT * FROM productos WHERE id = $id";
    $resultado = $conn->query($sql);
    $producto = $resultado->fetch_assoc();

    $subtotal = $producto['precio'] * $cantidad;
    $total_final += $subtotal;

    echo "<tr>";
    echo "<td>" . $producto['nombre'] . "</td>";
    echo "<td><img src='img/" . $producto['imagen'] . "' width='80'></td>";
    echo "<td>" . $producto['precio'] . " €</td>";
    echo "<td class='cantidad-carrito'>
        <a class='btn-cantidad' data-id='$id' data-action='restar'>−</a>
        <span class='num-cantidad' id='cantidad-$id'>$cantidad</span>
        <a class='btn-cantidad' data-id='$id' data-action='sumar'>+</a>

      </td>";
    echo "<td>$subtotal €</td>";
    echo "<td><a href='eliminar_carrito.php?id=$id'>🗑️</a></td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Total a pagar: $total_final €</h3>";
echo "<a href='finalizar_compra.php' style='
        display:inline-block;
        padding:10px 15px;
        background:#28a745;
        color:white;
        text-decoration:none;
        border-radius:5px;
        margin-top:20px;
     '>Finalizar compra</a>";

?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-cantidad").forEach(btn => {
        btn.addEventListener("click", () => {

            let id = btn.dataset.id;
            let action = btn.dataset.action;

            fetch("update_carrito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `id=${id}&action=${action}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) return;

                // actualizar cantidad
                document.getElementById(`cantidad-${id}`).textContent = data.cantidad;

                if (data.cantidad === 0) {
                    location.reload(); // si ya es 0, recarga para eliminar fila
                } else {
                    location.reload(); // también recarga para actualizar subtotal y total
                }
            });
        });
    });
});
</script>

</body>
</html>
