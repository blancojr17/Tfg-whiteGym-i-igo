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
$nombre = trim((string) ($_POST["nombre"] ?? ""));
$apellidos = trim((string) ($_POST["apellidos"] ?? ""));
$email = trim((string) ($_POST["email"] ?? ""));
$telefono = trim((string) ($_POST["telefono"] ?? ""));
$sexo = trim((string) ($_POST["sexo"] ?? ""));
$fecha_nacimiento = trim((string) ($_POST["fecha_nacimiento"] ?? ""));
$ciudad = trim((string) ($_POST["ciudad"] ?? ""));
$sexos_validos = ["hombre", "mujer", "otro", ""];

if ($nombre === "" || $apellidos === "" || $email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

if (!in_array($sexo, $sexos_validos, true)) {
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

if ($fecha_nacimiento !== "") {
    $fecha_valida = DateTime::createFromFormat("Y-m-d", $fecha_nacimiento);
    if (!$fecha_valida || $fecha_valida->format("Y-m-d") !== $fecha_nacimiento) {
        header("Location: ../../public/perfil.php?error=perfil");
        exit;
    }
}

if (strlen($nombre) > 80 || strlen($apellidos) > 120 || strlen($email) > 150 || strlen($telefono) > 30 || strlen($ciudad) > 120) {
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$sqlExiste = "SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario <> ? LIMIT 1";
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$stmtExiste->bind_param("si", $email, $id_usuario);
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$email_ocupado = $resExiste && $resExiste->num_rows > 0;
$stmtExiste->close();

if ($email_ocupado) {
    header("Location: ../../public/perfil.php?error=email");
    exit;
}

$sexo_sql = $sexo !== "" ? $sexo : null;
$fecha_nacimiento_sql = $fecha_nacimiento !== "" ? $fecha_nacimiento : null;
$telefono_sql = $telefono !== "" ? $telefono : null;
$ciudad_sql = $ciudad !== "" ? $ciudad : null;

$sqlUpdate = "UPDATE usuarios
              SET nombre = ?, apellidos = ?, email = ?, telefono = ?, sexo = ?, fecha_nacimiento = ?, ciudad = ?
              WHERE id_usuario = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$stmtUpdate->bind_param("sssssssi", $nombre, $apellidos, $email, $telefono_sql, $sexo_sql, $fecha_nacimiento_sql, $ciudad_sql, $id_usuario);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$stmtUpdate->close();
$_SESSION["nombre"] = $nombre;
$_SESSION["email"] = $email;

header("Location: ../../public/perfil.php?ok=perfil");
exit;
