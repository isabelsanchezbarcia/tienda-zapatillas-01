<?php
session_start();

// Si no existe el carrito, crearlo como array
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Obtener ID del producto desde la URL
$id_producto = $_GET['id'];

// Si ya existe el producto en el carrito → aumentar cantidad
if (isset($_SESSION['carrito'][$id_producto])) {
    $_SESSION['carrito'][$id_producto]++;
} else {
    $_SESSION['carrito'][$id_producto] = 1;
}

// Redirigir al carrito
header("Location: carrito.php");
exit;
?>
