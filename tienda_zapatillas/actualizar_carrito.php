<?php
session_start();
header('Content-Type: application/json');

require 'config.php';

/* comprobamos los datos de inicio de sesión */

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if (!isset($_POST['id'], $_POST['action'])) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$id = intval($_POST['id']);
$action = $_POST['action'];

/* verificar si el producto existe en la base de datos */

$stmt = $conn->prepare("SELECT id, precio FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'El producto no existe']);
    exit;
}

$producto = $result->fetch_assoc();
$precio = floatval($producto['precio']);


/* actualizar cantidad en el carrito */

// si no existe en el carrito y la acción es sumar se añade
if (!isset($_SESSION['carrito'][$id]) && $action === 'sumar') {
    $_SESSION['carrito'][$id] = 1;
}

// actualizar si existe
if (isset($_SESSION['carrito'][$id])) {

    if ($action === 'sumar') {

        // límite máximo opcional
        if ($_SESSION['carrito'][$id] < 20) {
            $_SESSION['carrito'][$id]++;
        }

    } elseif ($action === 'restar') {

        $_SESSION['carrito'][$id]--;

        if ($_SESSION['carrito'][$id] <= 0) {
            unset($_SESSION['carrito'][$id]);
        }
    }
}


// cantidad final
$cantidad = $_SESSION['carrito'][$id] ?? 0;


/* calcular el total del carrito */

$subtotal = $cantidad > 0 ? ($precio * $cantidad) : 0;

$total_final = 0;
foreach ($_SESSION['carrito'] as $prod_id => $cant) {

    $stmt2 = $conn->prepare("SELECT precio FROM productos WHERE id = ?");
    $stmt2->bind_param("i", $prod_id);
    $stmt2->execute();
    $resProd = $stmt2->get_result();

    if ($resProd->num_rows > 0) {
        $precio_prod = floatval($resProd->fetch_assoc()['precio']);
        $total_final += $precio_prod * $cant;
    }
}


/* respuesta JSON formateada */

echo json_encode([
    'cantidad' => $cantidad,
    'subtotal' => number_format($subtotal, 2, ',', '.'),
    'total' => number_format($total_final, 2, ',', '.')
]);
exit;
?>
