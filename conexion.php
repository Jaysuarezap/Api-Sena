<?php
// Configuración de las credenciales de la base de datos
$host = "localhost";
$user = "root"; // Un usuario por defecto de XAMPP
$pass = ""; //La contraseña por defecto
$db   = "api_sena_db";

// Creamos la conexión usando MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Comprobamos si hay errores en la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>