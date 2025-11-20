<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Tienda 01</title>
</head>

<body>

<?php include 'header.php'; ?>

<!-- ================= CONTENEDOR GENERAL ================= -->
<div class="filtros-y-productos">

    <!-- ================== CONSULTAS PARA LLENAR SELECTS ================== -->
    <?php
    $marcas_result = $conn->query("SELECT DISTINCT marca FROM productos ORDER BY marca");
    $colores_result = $conn->query("SELECT DISTINCT color FROM productos ORDER BY color");
    ?>

    <!-- ========== SIDEBAR DE FILTROS ========== -->
    <aside class="filtro-sidebar">
        <h3>Filtrar productos</h3>

        <form method="GET">

            <!-- MARCAS DINÁMICAS -->
            <label>Marca:</label>
            <select name="marca">
                <option value="">Todas</option>

                <?php while ($m = $marcas_result->fetch_assoc()) { ?>
                    <option value="<?php echo $m['marca']; ?>"
                        <?php if (!empty($_GET['marca']) && $_GET['marca'] == $m['marca']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($m['marca']); ?>
                    </option>
                <?php } ?>
            </select>

            <!-- COLORES DINÁMICOS -->
            <label>Color:</label>
            <select name="color">
                <option value="">Todos</option>

                <?php while ($c = $colores_result->fetch_assoc()) { ?>
                    <option value="<?php echo $c['color']; ?>"
                        <?php if (!empty($_GET['color']) && $_GET['color'] == $c['color']) echo "selected"; ?>>
                        <?php echo htmlspecialchars($c['color']); ?>
                    </option>
                <?php } ?>
            </select>

            <!-- PRECIOS -->
            <label>Precio mínimo:</label>
            <input type="number" name="min" step="0.01" value="<?php echo $_GET['min'] ?? ''; ?>">

            <label>Precio máximo:</label>
            <input type="number" name="max" step="0.01" value="<?php echo $_GET['max'] ?? ''; ?>">

            <button type="submit" class="btn-add" style="margin-top: 20px;">
                Aplicar filtros
            </button>

        </form>
    </aside>

    <!-- ========== LISTADO DE PRODUCTOS ========== -->

    <?php
    // construir consulta con filtros
    $sql = "SELECT * FROM productos WHERE 1=1";

    if (!empty($_GET['marca'])) {
        $marca = $conn->real_escape_string($_GET['marca']);
        $sql .= " AND marca = '$marca'";
    }

    if (!empty($_GET['color'])) {
        $color = $conn->real_escape_string($_GET['color']);
        $sql .= " AND color = '$color'";
    }

    if (!empty($_GET['min'])) {
        $min = floatval($_GET['min']);
        $sql .= " AND precio >= $min";
    }

    if (!empty($_GET['max'])) {
        $max = floatval($_GET['max']);
        $sql .= " AND precio <= $max";
    }

    $resultado = $conn->query($sql);
    ?>

    <div class="productos">
    <?php
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            ?>

            <div class="producto">
                <img src="img/<?php echo htmlspecialchars($fila['imagen']); ?>" 
                     alt="<?php echo htmlspecialchars($fila['nombre']); ?>">

                <h3><?php echo htmlspecialchars($fila['nombre']); ?></h3>

                <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>

                <p class="precio"><?php echo $fila['precio']; ?> €</p>

                <!-- Botón original sin tocar -->
                <button class="btn-add" data-id="<?php echo $fila['id']; ?>">
                    Añadir al carrito
                </button>
            </div>

            <?php
        }
    } else {
        echo "<p>No hay productos disponibles con esos filtros.</p>";
    }
    ?>
    </div>

</div> <!-- FIN DEL CONTENEDOR GLOBAL -->

<!-- ================= SCRIPT AÑADIR AL CARRITO ================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-add").forEach(btn => {
        btn.addEventListener("click", () => {

            let id = btn.dataset.id;

            fetch("añadir_carrito_ajax.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + id
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {

                    const bubble = document.querySelector(".burbuja-carrito");

                    if (bubble) {
                        bubble.textContent = data.total_items;
                    } else {
                        const link = document.querySelector(".carrito-link");
                        link.innerHTML += `<span class="burbuja-carrito">${data.total_items}</span>`;
                    }

                }
            });
        });
    });
});
</script>

</body>
</html>
