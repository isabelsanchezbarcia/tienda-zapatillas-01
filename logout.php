<?php
session_start();

// borrar las variables de sesión
$_SESSION = array();

// borrar las cookies de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// borrar la sesión
session_destroy();

// volver al inicio
header("Location: login.php"); //ARREGLAR, no redirige 
exit;
?>
