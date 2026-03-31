<?php
// 1. Configuración de errores y cabeceras
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// 2. Inclusión de la conexión (Asegúrate de que conexion.php existe)
include_once 'conexion.php';

// 3. Captura de datos JSON
$input = file_get_contents("php://input");
$data = json_decode($input);

// 4. Verificación de datos mínimos
if (isset($data->accion) && isset($data->usuario) && isset($data->password)) {
    
    $usuario = $data->usuario;
    $password = $data->password;

    // --- PROCESO DE REGISTRO ---
    if ($data->accion == "registro") {
        $correo = $data->correo ?? '';
        $telefono = $data->telefono ?? '';
        $rol = $data->rol ?? 'cliente';
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Ajustado a los nombres de columna que vimos en el error anterior
        $query = "INSERT INTO usuarios (usuario, password, correo, telefono, rol) 
                  VALUES ('$usuario', '$password_hash', '$correo', '$telefono', '$rol')";

        if ($conn->query($query) === TRUE) {
            echo json_encode(["status" => "success", "mensaje" => "Usuario registrado exitosamente."]);
        } else {
            echo json_encode(["status" => "error", "error" => "Error al registrar: " . $conn->error]);
        }
    }
    // --- PROCESO DE LOGIN ---
    elseif ($data->accion == "login") {
        $query = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conn->query($query);

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            // Verificamos con la columna 'password'
            if (password_verify($password, $fila['password'])) {
                echo json_encode([
                    "status" => "success",
                    "mensaje" => "Autenticación satisfactoria.",
                    "usuario" => $fila['usuario']
                ]);
            } else {
                echo json_encode(["status" => "error", "mensaje" => "Contraseña incorrecta."]);
            }
        } else {
            echo json_encode(["status" => "error", "mensaje" => "El usuario no existe."]);
        }
    }

} else {
    echo json_encode(["status" => "error", "mensaje" => "Faltan datos obligatorios (accion, usuario o password)."]);
}

// 5. Cierre de conexión
if (isset($conn) && $conn) {
    $conn->close();
}
?>