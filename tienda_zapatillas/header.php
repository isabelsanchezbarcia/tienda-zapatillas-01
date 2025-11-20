<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
  Calcula la "base" del proyecto en la URL de forma robusta.
  Busca el segmento '/tienda_zapatillas' en la ruta actual y toma todo hasta ahí.
  Si no lo encuentra, usa la carpeta padre del script como fallback.
*/
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);

if (preg_match('#^(.*?/tienda_zapatillas)#', $script, $m)) {
    $base = $m[1];
} else {
    // fallback: dirname del script (sin barra final)
    $base = rtrim(dirname($script), '/');
    if ($base === '') $base = '/';
}

// Saber si es admin
$esAdmin = isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin';
?>

<header>
    <h1>Tienda de Zapatillas</h1>

    <nav>
        <!-- Inicio -->
        <a href="<?php echo $base; ?>/index.php">Inicio</a>

        <?php if (!$esAdmin): ?> 
            <!-- SOLO usuarios NO admins ven el carrito -->
            <?php
            $carrito_count = isset($_SESSION['carrito']) 
                ? array_sum($_SESSION['carrito']) 
                : 0;
            ?>
            <a href="carrito.php" class="carrito-link">
                Carrito
                <?php if ($carrito_count > 0): ?>
                    <span class="burbuja-carrito"><?php echo $carrito_count; ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if (!isset($_SESSION['usuario_id'])): ?>

            <!-- Usuario no logueado -->
            <a href="<?php echo $base; ?>/login.php">Iniciar sesión</a>
            <a href="<?php echo $base; ?>/register.php">Registrarse</a>

        <?php else: ?>

            <!-- Usuario logueado -->
            <span style="color: #ddd; margin-left: 15px;">
                Hola, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
            </span>

            <?php if ($esAdmin): ?>
                <!-- Admin ve el panel -->
                <a href="<?php echo $base; ?>/admin/index.php">Panel admin</a>
            <?php else: ?>
                <!-- Usuario normal ve sus pedidos -->
                <a href="mis_pedidos.php">Mis pedidos</a>
            <?php endif; ?>

            <!-- Logout -->
            <a href="<?php echo $base; ?>/logout.php">Cerrar sesión</a>

        <?php endif; ?>
    </nav>
</header>
