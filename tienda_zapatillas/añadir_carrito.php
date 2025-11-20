<?php
session_start();

// si no existe el carrito, crearlo como array
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// obtener ID del producto desde la URL
$id_producto = $_GET['id'];

// si ya existe el producto en el carrito → aumentar cantidad
if (isset($_SESSION['carrito'][$id_producto])) {
    $_SESSION['carrito'][$id_producto]++;
} else {
    $_SESSION['carrito'][$id_producto] = 1;
}

// redirigir a la página anterior sin cambiar nada del diseño
$volver = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $volver");
exit;
?>
