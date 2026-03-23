<?php
// Configuración de las credenciales de la base de datos
$host = "localhost";
$user = "root"; // Aca un usuario por defecto de XAMPP
$pass = ""; //Aca Contraseña por defecto (vacía)
$db   = "api_sena_db";

// Creamos la conexión usando MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Comprobamos si hay errores en la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>