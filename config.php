<?php
$host = "127.0.0.1"; 
$db_name = "api_sena_db"; 
$username = "root"; // Cambiado a root
$password = "";     // Cambiado a contraseña vacía

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Error de conexión: " . $e->getMessage()]);
    exit;
}