<?php
require_once __DIR__ . "/../../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/registro.php");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$apellidos = trim($_POST["apellidos"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$telefono = trim($_POST["telefono"] ?? "");
$sexo = trim($_POST["sexo"] ?? "");
$fecha_nacimiento = trim($_POST["fecha_nacimiento"] ?? "");
$ciudad = trim($_POST["ciudad"] ?? "");

if ($nombre === "" || $apellidos === "" || $email === "" || $password === "" || $telefono === "" || $sexo === "" || $fecha_nacimiento === "" || $ciudad === "") {
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../public/registro.php?error=email");
    exit;
}

if (strlen($password) < 4) {
    header("Location: ../../public/registro.php?error=password");
    exit;
}

if (!in_array($sexo, ["hombre", "mujer", "otro"], true)) {
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

$fecha_valida = DateTime::createFromFormat("Y-m-d", $fecha_nacimiento);
if (!$fecha_valida || $fecha_valida->format("Y-m-d") !== $fecha_nacimiento) {
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

if (strlen($nombre) > 80 || strlen($apellidos) > 120 || strlen($telefono) > 30 || strlen($ciudad) > 120) {
    header("Location: ../../public/registro.php?error=campos");
    exit;
}

$sql = "select id_usuario from usuarios where email = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/registro.php?error=1");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: ../../public/registro.php?error=existe");
    exit;
}

$stmt->close();

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "insert into usuarios (nombre, apellidos, email, password, telefono, fecha_nacimiento, sexo, ciudad, rol, activo, fecha_registro)
        values (?, ?, ?, ?, ?, ?, ?, ?, 'usuario', 1, NOW())";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/registro.php?error=1");
    exit;
}

$stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $password_hash, $telefono, $fecha_nacimiento, $sexo, $ciudad);

if (!$stmt->execute()) {
    header("Location: ../../public/registro.php?error=1");
    exit;
}

header("Location: ../../public/login.php?registro=ok");
exit;
