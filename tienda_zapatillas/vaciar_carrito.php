<?php
session_start();

/* comprobar que el usuario existe */
if (!isset($_SESSION['usuario_id'])) {
    // si no está logeado redirigimos
    header("Location: login.php");
    exit;
}

/* comprobar el token CSRF */
if (!isset($_GET['token']) || !isset($_SESSION['csrf_token']) || $_GET['token'] !== $_SESSION['csrf_token']) {
    header("Location: carrito.php?error=csrf"); //no se especifica cual para no dar información
    exit;
}

/* vaciar el carrito pero no la sesión */
$_SESSION['carrito'] = []; 


/* regenerar token para que no se reutilice*/
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

/* redirgir al carrito*/
header("Location: carrito.php");
exit;
?>
