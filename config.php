<?php
// conexión a la base de datos
$servidor = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "tienda_zapatillas";

// aqui estoy creando la conexión
$conn = new mysqli($servidor, $usuario, $contraseña, $base_datos);

// para comprobar si hay error
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} // si no da error no muestro nada
?>
