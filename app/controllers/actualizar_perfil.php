<?php
// actualizacion del perfil del usuario
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/perfil.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
// recogida de datos del formulario
$nombre = trim((string) ($_POST["nombre"] ?? ""));
// recogida de datos del formulario
$apellidos = trim((string) ($_POST["apellidos"] ?? ""));
// recogida de datos del formulario
$email = trim((string) ($_POST["email"] ?? ""));
// recogida de datos del formulario
$telefono = trim((string) ($_POST["telefono"] ?? ""));
// recogida de datos del formulario
$sexo = trim((string) ($_POST["sexo"] ?? ""));
// recogida de datos del formulario
$fecha_nacimiento = trim((string) ($_POST["fecha_nacimiento"] ?? ""));
// recogida de datos del formulario
$ciudad = trim((string) ($_POST["ciudad"] ?? ""));
$sexos_validos = ["hombre", "mujer", "otro", ""];

if ($nombre === "" || $apellidos === "" || $email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
// redireccion final
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

if (!in_array($sexo, $sexos_validos, true)) {
// redireccion final
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

if ($fecha_nacimiento !== "") {
    $fecha_valida = DateTime::createFromFormat("Y-m-d", $fecha_nacimiento);
    if (!$fecha_valida || $fecha_valida->format("Y-m-d") !== $fecha_nacimiento) {
// redireccion final
        header("Location: ../../public/perfil.php?error=perfil");
        exit;
    }
}

if (strlen($nombre) > 80 || strlen($apellidos) > 120 || strlen($email) > 150 || strlen($telefono) > 30 || strlen($ciudad) > 120) {
// redireccion final
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

// consulta sql
$sqlExiste = "SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario <> ? LIMIT 1";
// preparacion de la consulta
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
// redireccion final
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$stmtExiste->bind_param("si", $email, $id_usuario);
// ejecucion de la consulta
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$email_ocupado = $resExiste && $resExiste->num_rows > 0;
$stmtExiste->close();

if ($email_ocupado) {
// redireccion final
    header("Location: ../../public/perfil.php?error=email");
    exit;
}

$sexo_sql = $sexo !== "" ? $sexo : null;
$fecha_nacimiento_sql = $fecha_nacimiento !== "" ? $fecha_nacimiento : null;
$telefono_sql = $telefono !== "" ? $telefono : null;
$ciudad_sql = $ciudad !== "" ? $ciudad : null;

// consulta sql
$sqlUpdate = "UPDATE usuarios
              SET nombre = ?, apellidos = ?, email = ?, telefono = ?, sexo = ?, fecha_nacimiento = ?, ciudad = ?
              WHERE id_usuario = ?";
// preparacion de la consulta
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
// redireccion final
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$stmtUpdate->bind_param("sssssssi", $nombre, $apellidos, $email, $telefono_sql, $sexo_sql, $fecha_nacimiento_sql, $ciudad_sql, $id_usuario);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
// redireccion final
    header("Location: ../../public/perfil.php?error=perfil");
    exit;
}

$stmtUpdate->close();
$_SESSION["nombre"] = $nombre;
$_SESSION["email"] = $email;

// redireccion final
header("Location: ../../public/perfil.php?ok=perfil");
exit;

