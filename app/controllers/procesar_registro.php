<?php
// registro de nuevos usuarios
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/registro.php");
    exit;
}

// recogida de datos del formulario
$nombre = trim($_POST["nombre"] ?? "");
// recogida de datos del formulario
$apellidos = trim($_POST["apellidos"] ?? "");
// recogida de datos del formulario
$email = trim($_POST["email"] ?? "");
// recogida de datos del formulario
$password = $_POST["password"] ?? "";
// recogida de datos del formulario
$telefono = trim($_POST["telefono"] ?? "");
// recogida de datos del formulario
$sexo = trim($_POST["sexo"] ?? "");
// recogida de datos del formulario
$fecha_nacimiento = trim($_POST["fecha_nacimiento"] ?? "");
// recogida de datos del formulario
$ciudad = trim($_POST["ciudad"] ?? "");

if ($nombre === "" || $apellidos === "" || $email === "" || $password === "" || $telefono === "" || $sexo === "" || $fecha_nacimiento === "" || $ciudad === "") {
// redireccion final
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
// redireccion final
    header("Location: ../../public/registro.php?error=email");
    exit;
}

if (strlen($password) < 4) {
// redireccion final
    header("Location: ../../public/registro.php?error=password");
    exit;
}

if (!in_array($sexo, ["hombre", "mujer", "otro"], true)) {
// redireccion final
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

$fecha_valida = DateTime::createFromFormat("Y-m-d", $fecha_nacimiento);
if (!$fecha_valida || $fecha_valida->format("Y-m-d") !== $fecha_nacimiento) {
// redireccion final
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

if (strlen($nombre) > 80 || strlen($apellidos) > 120 || strlen($telefono) > 30 || strlen($ciudad) > 120) {
// redireccion final
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

// consulta sql
$sql = "select id_usuario from usuarios where email = ?";
// preparacion de la consulta
$stmt = $conexion->prepare($sql);

if (!$stmt) {
// redireccion final
    header("Location: ../../public/registro.php?error=1");
    exit;
}

$stmt->bind_param("s", $email);
// ejecucion de la consulta
$stmt->execute();
$stmt->store_result();

// comprobacion de la consulta
if ($stmt->num_rows > 0) {
// redireccion final
    header("Location: ../../public/registro.php?error=existe");
    exit;
}

$stmt->close();

// cifrado de la contrasena
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// consulta sql
$sql = "insert into usuarios (nombre, apellidos, email, password, telefono, fecha_nacimiento, sexo, ciudad, rol, activo, fecha_registro)
        values (?, ?, ?, ?, ?, ?, ?, ?, 'usuario', 1, NOW())";
// preparacion de la consulta
$stmt = $conexion->prepare($sql);

if (!$stmt) {
// redireccion final
    header("Location: ../../public/registro.php?error=1");
    exit;
}

$stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $password_hash, $telefono, $fecha_nacimiento, $sexo, $ciudad);

if (!$stmt->execute()) {
// redireccion final
    header("Location: ../../public/registro.php?error=1");
    exit;
}

// redireccion final
header("Location: ../../public/login.php?registro=ok");
exit;

