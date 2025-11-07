<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header> 
    <h1>Tienda de Zapatillas</h1>

    <nav>
        <a href="index.php">Inicio</a>
        <a href="carrito.php">Carrito</a>

        <?php if (!isset($_SESSION['usuario_id'])): ?>
            <a href="login.php">Iniciar sesión</a>
            <a href="register.php">Registrarse</a>
        <?php else: ?>
            <span style="color: #ddd; margin-left: 15px;">
                Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
            </span>
            <a href="logout.php">Cerrar sesión</a> 
        <?php endif; ?>
    </nav>
</header>