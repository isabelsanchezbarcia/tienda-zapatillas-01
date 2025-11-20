<?php
session_start();

$id = $_GET['id'];

if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]++;
}

header("Location: carrito.php");
exit;
?>