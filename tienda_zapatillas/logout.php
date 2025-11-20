<?php
session_start();

/* eliminar todas lasvariables */
$_SESSION = [];

/* eliminar la cookie de sesión */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 3600,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* destruir la sesión */
session_destroy();

/* redirección al login oficial */

$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);

if (preg_match('#^(.*?/tienda_zapatillas)#', $script, $m)) {
    $base = $m[1];
} else {
    $base = '/';
}

header("Location: $base/login.php");
exit;
