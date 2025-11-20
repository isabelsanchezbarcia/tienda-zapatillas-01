<?php
session_start();

$id = $_GET['id'];

if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]--;

    // si la cantidad llega a 0, eliminarlo
    if ($_SESSION['carrito'][$id] <= 0) {
        unset($_SESSION['carrito'][$id]);
    }
}

header("Location: carrito.php");
exit;
?>