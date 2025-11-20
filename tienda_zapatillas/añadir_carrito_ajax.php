<?php
session_start();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$id = intval($_POST['id']);

if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]++;
} else {
    $_SESSION['carrito'][$id] = 1;
}

echo json_encode([
    "success" => true,
    "total_items" => array_sum($_SESSION['carrito'])
]);

exit;
?>
