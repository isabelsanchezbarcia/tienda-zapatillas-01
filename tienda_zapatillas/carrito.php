<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
include 'config.php';

/* validación inicial carrito */

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/estilos.css">
        <title>Carrito vacío</title>
    </head>
    <body>

    <?php include 'header.php'; ?>

    <div class="carrito-vacio">
        <img src="img/compraRealizadaConExito.png" alt="Carrito vacío" class="carrito-vacio-img">
        <h2>Tu carrito está vacío</h2>
        <p>Explora nuestros productos y encuentra algo que te encante.</p>
        <a href="index.php" class="btn-volver-tienda">Volver a la tienda</a>
    </div>

    </body>
    </html>
    <?php
    exit;
}


/* función segura para obtener el producto */

function obtenerProducto($conn, $id)
{
    $stmt = $conn->prepare("SELECT id, nombre, descripcion, precio, imagen 
                            FROM productos 
                            WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

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

<div class="carrito-contenedor">
<h1 style="text-align:center; margin-bottom:25px;">Tu carrito</h1>

<table class="tabla-carrito">
    <tr>
        <th>Producto</th>
        <th>Imagen</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
        <th>Eliminar</th>
    </tr>

<?php
$total_final = 0;

foreach ($_SESSION['carrito'] as $id => $cantidad):

    $id = intval($id);
    $cantidad = intval($cantidad);

    if ($cantidad < 1) continue;

    $producto = obtenerProducto($conn, $id);
    if (!$producto) continue;

    $precio = floatval($producto['precio']);
    $subtotal = $precio * $cantidad;
    $total_final += $subtotal;

    $precio_fmt = number_format($precio, 2, ',', '.');
    $subtotal_fmt = number_format($subtotal, 2, ',', '.');

?>
    <tr data-id="<?= $id ?>">
        <td><?= htmlspecialchars($producto['nombre']) ?></td>

        <td><img src="img/<?= htmlspecialchars($producto['imagen']) ?>" width="80" class="carrito-img"></td>

        <td><?= $precio_fmt ?> €</td>

        <td class="cantidad-carrito">
            <button type="button" class="btn-cantidad" data-id="<?= $id ?>" data-action="restar">−</button>
            <span id="cantidad-<?= $id ?>" class="num-cantidad"><?= $cantidad ?></span>
            <button type="button" class="btn-cantidad" data-id="<?= $id ?>" data-action="sumar">+</button>
        </td>

        <td id="subtotal-<?= $id ?>"><?= $subtotal_fmt ?> €</td>

        <td>
            <a class="btn-eliminar" href="eliminar_carrito.php?id=<?= $id ?>">Quitar</a>
        </td>
    </tr>

<?php endforeach; ?>

</table>

<?php $total_fmt = number_format($total_final, 2, ',', '.'); ?>
<p id="total-final" class="total-final">Total: <?= $total_fmt ?> €</p>


<!-- botones -->
<div class="botones-carrito">
    <div>
        <a class="btn-seguir" href="index.php">Seguir comprando</a>
        <a class='btn-vaciar' 
           href='vaciar_carrito.php?token=<?php echo $_SESSION["csrf_token"]; ?>'>
           Vaciar carrito
        </a>

    </div>
    <a href="finalizar_compra.php" class="btn-finalizar">Finalizar compra</a>
</div>

</div>


<!-- script para actualizar cantidad -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    async function postData(url, data) {
        const params = new URLSearchParams(data);

        const res = await fetch(url, {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: params.toString()
        });

        if (!res.ok) throw new Error("HTTP " + res.status);
        return await res.json();
    }

    document.querySelectorAll(".btn-cantidad").forEach(btn => {
        btn.addEventListener("click", async () => {

            const id = btn.dataset.id;
            const action = btn.dataset.action;

            try {
                const data = await postData("actualizar_carrito.php", { id, action });

                if (data.error) {
                    alert(data.error);
                    return;
                }

                document.getElementById("cantidad-" + id).textContent = data.cantidad;
                document.getElementById("subtotal-" + id).textContent = data.subtotal + " €";
                document.getElementById("total-final").textContent = "Total: " + data.total + " €";

                if (data.cantidad == 0) {
                    document.querySelector("tr[data-id='" + id + "']").remove();
                }

            } catch (e) {
                console.error(e);
                alert("Error al actualizar el carrito.");
            }

        });
    });

});
</script>

</body>
</html>
