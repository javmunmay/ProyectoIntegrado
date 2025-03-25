<?php
// Datos de conexión a la base de datos
$servername = "PMYSQL185.dns-servicio.com:3306";
$username = "Javier";
$password = "t33eq3*N6";
$dbname = "10858837_rallyfotografico";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
