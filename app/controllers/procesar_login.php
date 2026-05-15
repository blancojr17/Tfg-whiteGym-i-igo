<?php
// inicio de sesion del usuario
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// recogida de datos del formulario
$email = trim($_POST["email"] ?? "");
// recogida de datos del formulario
$password = $_POST["password"] ?? "";
$redirect_query = $email !== "" ? "&" . http_build_query(["email" => $email]) : "";

if ($email === "" || $password === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
// redireccion final
    header("Location: ../../public/login.php?error=campos" . $redirect_query);
    exit;
}

// consulta sql
$sql = "select id_usuario, nombre, email, password, rol, activo from usuarios where email = ?";

// preparacion de la consulta
$stmt = $conexion->prepare($sql);

if (!$stmt) {
// redireccion final
    header("Location: ../../public/login.php?error=1" . $redirect_query);
    exit;
}

$stmt->bind_param("s", $email);
// ejecucion de la consulta
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
// lectura de resultados
    $fila = $resultado->fetch_assoc();

// comprobacion de credenciales
    if ($fila["activo"] == 1 && password_verify($password, $fila["password"])) {
// regeneracion de la sesion
        session_regenerate_id(true);

        $_SESSION["id_usuario"] = $fila["id_usuario"];
        $_SESSION["nombre"] = $fila["nombre"];
        $_SESSION["email"] = $fila["email"];
        $_SESSION["rol"] = $fila["rol"];

        if ($fila["rol"] === "admin") {
// redireccion final
            header("Location: ../../public/admin.php");
        } elseif ($fila["rol"] === "entrenador") {
// redireccion final
            header("Location: ../../public/entrenador.php");
        } else {
// redireccion final
            header("Location: ../../public/cliente.php");
        }
        exit;
    }
}

// redireccion final
header("Location: ../../public/login.php?error=1" . $redirect_query);
exit;

