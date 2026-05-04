<?php
require __DIR__ . "/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /WHITEGYM/php/registro.php");
    exit;
}

$nombre = trim($_POST["nombre"]);
$apellidos = trim($_POST["apellidos"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];

$sql = "select id from usuarios where email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: /WHITEGYM/php/registro.php?error=1");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "insert into usuarios (nombre, apellidos, email, password, rol, activo)
        values (?, ?, ?, ?, 'cliente', 1)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssss", $nombre, $apellidos, $email, $password_hash);
$stmt->execute();

header("Location: /WHITEGYM/php/login.php?registro=ok");
exit;
