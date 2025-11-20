<?php
session_start();
include 'config.php';

// si el carrito está vacio volvemos al carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit;
}

// verificar que el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION['usuario_id'];
$fecha = date('Y-m-d H:i:s');
$total = 0;
$detalles = [];

// Iniciamos transacción 
$conn->begin_transaction();

try {
    // consulta preparada para obtener datos de cada producto
    $stmt_prod = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
    if (!$stmt_prod) {
        throw new Exception("Error preparando SELECT productos: " . $conn->error);
    }

    // recorremos carrito y calculamos totales
    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $id = (int) $id;
        $cantidad = max(1, (int) $cantidad); // nunca menos de 1

        $stmt_prod->bind_param("i", $id);
        $stmt_prod->execute();
        $resultado = $stmt_prod->get_result();

        if ($resultado->num_rows === 0) {
            // si un producto ya no existe, lo ignoramos
            continue;
        }

        $producto = $resultado->fetch_assoc();
        $precio   = (float) $producto['precio'];
        $subtotal = $precio * $cantidad;
        $total   += $subtotal;

        $detalles[] = [
            'id'       => $id,
            'nombre'   => $producto['nombre'],
            'precio'   => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal
        ];
    }

    $stmt_prod->close();

    // si por algun motivo no hay detalles 
    if (empty($detalles)) {
        // revertimos por seguridad
        $conn->rollback();
        header("Location: carrito.php");
        exit;
    }

    // codificamos detalles a JSON
    $detalles_json = json_encode($detalles, JSON_UNESCAPED_UNICODE);

    // consulta preparada para insertar el pedido
    $stmt_pedido = $conn->prepare("
        INSERT INTO pedidos (id_usuario, fecha, total, detalles)
        VALUES (?, ?, ?, ?)
    ");
    if (!$stmt_pedido) {
        throw new Exception("Error preparando INSERT pedidos: " . $conn->error);
    }

    // id_usuario (int), fecha (string), total (double), detalles_json (string)
    $stmt_pedido->bind_param("isds", $id_usuario, $fecha, $total, $detalles_json);

    $ok = $stmt_pedido->execute();
    if (!$ok) {
        throw new Exception("Error ejecutando INSERT pedido: " . $stmt_pedido->error);
    }

    $stmt_pedido->close();

    // si todo ha ido bien hacemos COMMIT y vaciamos carrito
    $conn->commit();
    unset($_SESSION['carrito']);

} catch (Exception $e) {
    // si algo falla deshacemos todo
    $conn->rollback();
    $ok = false;
    $error_msg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra finalizada</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php include "header.php"; ?>

<?php if (!empty($ok) && $ok): ?>

    <div class="compra-exito-contenedor">
        <div class="compra-exito-card">
            <img src="img/compraRealizadaConExito.png" alt="Éxito" class="compra-exito-icon">

            <h2>¡Compra realizada con éxito!</h2>
            <p>
                Gracias por confiar en nosotros.<br>
                Tu pedido ha sido registrado correctamente.
            </p>

            <a href="index.php" class="btn-volver-tienda">Volver a la tienda</a>
        </div>
    </div>

<?php else: ?>

    <div class="compra-exito-contenedor">
        <div class="compra-exito-card" style="border-left:5px solid red;">
            <h2 style="color:red;">Error en el pedido</h2>
            <p>
                No se pudo registrar el pedido.
                <?php if (!empty($error_msg)): ?>
                    <br><small><?php echo htmlspecialchars($error_msg); ?></small>
                <?php endif; ?>
            </p>
            <a href="carrito.php" class="btn-volver-tienda">Volver al carrito</a>
        </div>
    </div>

<?php endif; ?>

</body>
</html>
