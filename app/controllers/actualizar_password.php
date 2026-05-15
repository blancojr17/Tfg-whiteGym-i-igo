<?php
// actualizacion de la contrasena del usuario
// inicio de sesiÃ³n y conexiÃ³n con la base de datos
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// comprobaciÃ³n de acceso solo para usuarios
// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// validaciÃ³n del mÃ©todo post
// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/perfil.php");
    exit;
}

// recogida de datos enviados desde el formulario
$id_usuario = (int) $_SESSION["id_usuario"];
// recogida de datos del formulario
$password_actual = (string) ($_POST["password_actual"] ?? "");
// recogida de datos del formulario
$password_nueva = (string) ($_POST["password_nueva"] ?? "");
// recogida de datos del formulario
$password_repetida = (string) ($_POST["password_repetida"] ?? "");

// comprobaciÃ³n de campos vacÃ­os
if ($password_actual === "" || $password_nueva === "" || $password_repetida === "") {
// redireccion final
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

// validaciÃ³n de longitud y coincidencia de contraseÃ±a
if (strlen($password_nueva) < 4 || $password_nueva !== $password_repetida) {
// redireccion final
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

// obtenciÃ³n de contraseÃ±a actual del usuario
// consulta sql
$sqlUsuario = "SELECT password FROM usuarios WHERE id_usuario = ? LIMIT 1";
// preparacion de la consulta
$stmtUsuario = $conexion->prepare($sqlUsuario);

// comprobaciÃ³n de preparaciÃ³n sql
if (!$stmtUsuario) {
// redireccion final
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

// ejecuciÃ³n de consulta
$stmtUsuario->bind_param("i", $id_usuario);
// ejecucion de la consulta
$stmtUsuario->execute();
$resUsuario = $stmtUsuario->get_result();
// lectura de resultados
$usuario = $resUsuario ? $resUsuario->fetch_assoc() : null;
$stmtUsuario->close();

// comprobaciÃ³n de contraseÃ±a actual
// comprobacion de credenciales
if (!$usuario || !password_verify($password_actual, $usuario["password"] ?? "")) {
// redireccion final
    header("Location: ../../public/perfil.php?error=actual");
    exit;
}

// generaciÃ³n de nueva contraseÃ±a encriptada
// cifrado de la contrasena
$password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

// actualizaciÃ³n de contraseÃ±a en base de datos
// consulta sql
$sqlUpdate = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
// preparacion de la consulta
$stmtUpdate = $conexion->prepare($sqlUpdate);

// comprobaciÃ³n de preparaciÃ³n sql
if (!$stmtUpdate) {
// redireccion final
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

// asociaciÃ³n de parÃ¡metros
$stmtUpdate->bind_param("si", $password_hash, $id_usuario);

// ejecuciÃ³n de actualizaciÃ³n
if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
// redireccion final
    header("Location: ../../public/perfil.php?error=password");
    exit;
}

// cierre y redirecciÃ³n final
$stmtUpdate->close();
// redireccion final
header("Location: ../../public/perfil.php?ok=password");
exit;
