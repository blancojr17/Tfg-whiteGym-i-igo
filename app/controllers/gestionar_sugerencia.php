<?php
// gestion de sugerencias
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/admin_sugerencias.php?error=metodo");
    exit;
}

// recogida de datos del formulario
$redirect_query = trim((string) ($_POST["redirect_query"] ?? ""));
$redirect_params = [];
if ($redirect_query !== "") {
    parse_str($redirect_query, $redirect_params);
}

$redirect_filtrado = [];
if (isset($redirect_params["estado"]) && in_array($redirect_params["estado"], ["todos", "pendiente", "leido"], true)) {
    $redirect_filtrado["estado"] = $redirect_params["estado"];
}
if (isset($redirect_params["page"]) && (int) $redirect_params["page"] > 0) {
    $redirect_filtrado["page"] = (int) $redirect_params["page"];
}

$redirect_suffix = $redirect_filtrado ? "&" . http_build_query($redirect_filtrado) : "";

// recogida de datos del formulario
$id_sugerencia = (int) ($_POST["id_sugerencia"] ?? 0);
// recogida de datos del formulario
$accion = trim((string) ($_POST["accion"] ?? ""));

if ($id_sugerencia <= 0 || $accion !== "marcar_leido") {
// redireccion final
    header("Location: ../../public/admin_sugerencias.php?error=datos" . $redirect_suffix);
    exit;
}

// consulta sql
$sql = "UPDATE sugerencias
        SET estado = 'leido'
        WHERE id_sugerencia = ? AND estado = 'pendiente'";
// preparacion de la consulta
$stmt = $conexion->prepare($sql);

if (!$stmt) {
// redireccion final
    header("Location: ../../public/admin_sugerencias.php?error=1" . $redirect_suffix);
    exit;
}

$stmt->bind_param("i", $id_sugerencia);

if (!$stmt->execute()) {
    $stmt->close();
// redireccion final
    header("Location: ../../public/admin_sugerencias.php?error=1" . $redirect_suffix);
    exit;
}

$stmt->close();
// redireccion final
header("Location: ../../public/admin_sugerencias.php?ok=actualizada" . $redirect_suffix);
exit;

