<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/admin_sugerencias.php?error=metodo");
    exit;
}

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

$id_sugerencia = (int) ($_POST["id_sugerencia"] ?? 0);
$accion = trim((string) ($_POST["accion"] ?? ""));

if ($id_sugerencia <= 0 || $accion !== "marcar_leido") {
    header("Location: ../../public/admin_sugerencias.php?error=datos" . $redirect_suffix);
    exit;
}

$sql = "UPDATE sugerencias
        SET estado = 'leido'
        WHERE id_sugerencia = ? AND estado = 'pendiente'";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/admin_sugerencias.php?error=1" . $redirect_suffix);
    exit;
}

$stmt->bind_param("i", $id_sugerencia);

if (!$stmt->execute()) {
    $stmt->close();
    header("Location: ../../public/admin_sugerencias.php?error=1" . $redirect_suffix);
    exit;
}

$stmt->close();
header("Location: ../../public/admin_sugerencias.php?ok=actualizada" . $redirect_suffix);
exit;
