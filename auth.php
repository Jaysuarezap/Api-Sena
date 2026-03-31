<?php
// 1. Cabeceras primero
header("Content-Type: application/json");

// 2. Cargar configuración - Usamos require_once para mayor seguridad
require_once __DIR__ . '/config.php';

// 3. Verificar si $pdo existe, si no, lanzar un error claro de JSON
if (!isset($pdo)) {
    echo json_encode(["error" => "La conexión a la base de datos no se estableció correctamente."]);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Capturamos los datos
$usuario  = $data['usuario'] ?? null;
$password = $data['password'] ?? null;
$correo   = $data['correo'] ?? null;
$telefono = $data['telefono'] ?? null;
$rol      = $data['rol'] ?? 'cliente';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $data['accion'] ?? 'login';

    if ($accion == 'registro') {
        if (!$usuario || !$password || !$correo) {
            echo json_encode(["error" => "Usuario, contraseña y correo son obligatorios"]);
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        try {
            // Usamos la variable $pdo que viene de config.php
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, password_hash, correo, telefono, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$usuario, $hash, $correo, $telefono, $rol]);
            echo json_encode(["status" => "success", "mensaje" => "Usuario registrado con perfil completo"]);
        } catch (Exception $e) {
            echo json_encode(["error" => "Error en el registro: " . $e->getMessage()]);
        }

    } else {
        // Lógica de Login
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
            $stmt->execute([$usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                echo json_encode([
                    "status" => "success",
                    "mensaje" => "Autenticación satisfactoria",
                    "datos_usuario" => [
                        "nombre" => $user['nombre_usuario'],
                        "rol"    => $user['rol'],
                        "correo" => $user['correo']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["status" => "error", "mensaje" => "Error en la autenticación"]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => "Error en el servidor: " . $e->getMessage()]);
        }
    }
}