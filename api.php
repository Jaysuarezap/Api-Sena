<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Aca permito solicitudes desde cualquier origen (CORS) y definimos que devolveremos JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Incluyo el archivo de conexión a la base de datos
include_once 'conexion.php';

// Aca obtengo los datos enviados en formato JSON
$data = json_decode(file_get_contents("php://input"));

// Aca verifico que se haya enviado una acción (registro o login)
if (isset($data->accion)) {
    
    $usuario = $data->usuario;
    $password = $data->password;

    
    // Aca realizo el proceso de registro o login dependiendo de la acción solicitada
    if ($data->accion == "registro") {
        // Encripto la contraseña por seguridad
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Preparo la consulta SQL para insertar el usuario
        $query = "INSERT INTO usuarios (usuario, password) VALUES ('$usuario', '$password_hash')";

        if ($conn->query($query) === TRUE) {
            // Respuesta exitosa
            echo json_encode(["mensaje" => "Usuario registrado exitosamente."]);
        } else {
            // Respuesta de error
            echo json_encode(["error" => "Error al registrar el usuario: " . $conn->error]);
        }
    }


    // INICIO DE SESIÓN (LOGIN)

    elseif ($data->accion == "login") {
        // Busco el usuario en la base de datos
        $query = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conn->query($query);

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            
            // Verifico si la contraseña enviada coincide con la encriptada en la BD
            if (password_verify($password, $fila['password'])) {
                // Autenticación correcta 
                echo json_encode(["mensaje" => "Autenticación satisfactoria."]);
            } else {
                // Contraseña incorrecta 
                echo json_encode(["error" => "Error en la autenticación. Contraseña incorrecta."]);
            }
        } else {
            // Usuario no encontrado 
            echo json_encode(["error" => "Error en la autenticación. Usuario no existe."]);
        }
    }
} else {
    // Si no se especifica una acción
    echo json_encode(["error" => "No se especificó ninguna acción (registro o login)."]);
}
// ... resto de tu código arriba ...

// Verificar si la variable existe antes de intentar cerrarla
if (isset($conn) && $conn) {
    $conn->close();
}
?>