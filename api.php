<?php
// Configuración de visualización de errores (Opcional en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Encabezados para permitir solicitudes CORS y definir formato JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Inclusión del archivo de conexión a la base de datos
include_once 'conexion.php';

// Captura de los datos enviados en el cuerpo de la petición (JSON)
$data = json_decode(file_get_contents("php://input"));

// Verificación de que se recibió una acción y los datos necesarios
if (isset($data->accion) && isset($data->usuario) && isset($data->password)) {
    
    $usuario = $data->usuario;
    $password = $data->password;

    // ==========================================
    // PROCESO DE REGISTRO
    // ==========================================
    if ($data->accion == "registro") {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (usuario, password) VALUES ('$usuario', '$password_hash')";

        if ($conn->query($query) === TRUE) {
            echo json_encode(["mensaje" => "Usuario registrado exitosamente."]);
        } else {
            echo json_encode(["error" => "Error al registrar el usuario: " . $conn->error]);
        }
    }

    // ==========================================
    // PROCESO DE ACTUALIZACIÓN
    // ==========================================
    elseif ($data->accion == "actualizar") {
        $nueva_password_hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE usuarios SET password = '$nueva_password_hash' WHERE usuario = '$usuario'";

        if ($conn->query($query) === TRUE) {
            echo json_encode(["mensaje" => "Contraseña actualizada correctamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar: " . $conn->error]);
        }
    }

    // ==========================================
    // PROCESO DE INICIO DE SESIÓN (LOGIN)
    // ==========================================
    elseif ($data->accion == "login") {
        $query = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conn->query($query);

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            
            // Verificación de contraseña contra el hash de la BD
            if (password_verify($password, $fila['password'])) {
                echo json_encode(["mensaje" => "Autenticación satisfactoria."]);
            } else {
                echo json_encode(["error" => "Error en la autenticación. Contraseña incorrecta."]);
            }
        } else {
            echo json_encode(["error" => "Error en la autenticación. Usuario no existe."]);
        }
    }
} else {
    // Respuesta por defecto si no se cumplen los requisitos de entrada
    echo json_encode(["error" => "No se especificó una acción válida o faltan datos (usuario/password)."]);
}

// Cierre seguro de la conexión a la base de datos
if (isset($conn) && $conn) {
    $conn->close();
}
?>