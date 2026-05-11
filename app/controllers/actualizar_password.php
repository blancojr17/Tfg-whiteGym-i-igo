<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/perfil.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$password_actual = (string) ($_POST["password_actual"] ?? "");
$password_nueva = (string) ($_POST["password_nueva"] ?? "");
$password_repetida = (string) ($_POST["password_repetida"] ?? "");

if ($password_actual === "" || $password_nueva === "" || $password_repetida === "") {
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

if (strlen($password_nueva) < 4 || $password_nueva !== $password_repetida) {
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

$sqlUsuario = "SELECT password FROM usuarios WHERE id_usuario = ? LIMIT 1";
$stmtUsuario = $conexion->prepare($sqlUsuario);

if (!$stmtUsuario) {
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resUsuario = $stmtUsuario->get_result();
$usuario = $resUsuario ? $resUsuario->fetch_assoc() : null;
$stmtUsuario->close();

if (!$usuario || !password_verify($password_actual, $usuario["password"] ?? "")) {
    header("Location: ../../public/perfil.php?error=actual");
    exit;
}

$password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
$sqlUpdate = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

$stmtUpdate->bind_param("si", $password_hash, $id_usuario);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

$stmtUpdate->close();
header("Location: ../../public/perfil.php?ok=password");
exit;
