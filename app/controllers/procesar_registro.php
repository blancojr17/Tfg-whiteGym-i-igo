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

if ($nombre === "" || $apellidos === "" || $email === "" || $password === "") {
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

$sql = "insert into usuarios (nombre, apellidos, email, password, rol, activo)
        values (?, ?, ?, ?, 'usuario', 1)";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/registro.php?error=1");
    exit;
}

$stmt->bind_param("ssss", $nombre, $apellidos, $email, $password_hash);

if (!$stmt->execute()) {
    header("Location: ../../public/registro.php?error=1");
    exit;
}

header("Location: ../../public/login.php?registro=ok");
exit;
