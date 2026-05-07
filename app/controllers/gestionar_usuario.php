<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/admin_usuarios.php?error=metodo");
    exit;
}

$redirect_query = trim((string) ($_POST["redirect_query"] ?? ""));
$redirect_params = [];
if ($redirect_query !== "") {
    parse_str($redirect_query, $redirect_params);
}

$redirect_filtrado = [];
if (isset($redirect_params["q"]) && is_string($redirect_params["q"])) {
    $redirect_filtrado["q"] = $redirect_params["q"];
}
if (isset($redirect_params["rol"]) && in_array($redirect_params["rol"], ["todos", "usuario", "entrenador", "admin"], true)) {
    $redirect_filtrado["rol"] = $redirect_params["rol"];
}
if (isset($redirect_params["page"]) && (int) $redirect_params["page"] > 0) {
    $redirect_filtrado["page"] = (int) $redirect_params["page"];
}

$redirect_suffix = $redirect_filtrado ? "&" . http_build_query($redirect_filtrado) : "";

$id_admin_actual = (int) $_SESSION["id_usuario"];
$id_usuario = (int) ($_POST["id_usuario"] ?? 0);
$rol = trim($_POST["rol"] ?? "");
$activo = $_POST["activo"] ?? "";

$roles_validos = ["usuario", "entrenador", "admin"];

if ($id_usuario <= 0) {
    header("Location: ../../public/admin_usuarios.php?error=id" . $redirect_suffix);
    exit;
}

if (!in_array($rol, $roles_validos, true)) {
    header("Location: ../../public/admin_usuarios.php?error=rol" . $redirect_suffix);
    exit;
}

if ($activo !== "0" && $activo !== "1") {
    header("Location: ../../public/admin_usuarios.php?error=activo" . $redirect_suffix);
    exit;
}

$activo_int = (int) $activo;

if ($id_usuario === $id_admin_actual && ($rol !== "admin" || $activo_int !== 1)) {
    header("Location: ../../public/admin_usuarios.php?error=autoproteccion" . $redirect_suffix);
    exit;
}

$sqlExiste = "SELECT id_usuario FROM usuarios WHERE id_usuario = ? LIMIT 1";
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
    header("Location: ../../public/admin_usuarios.php?error=1" . $redirect_suffix);
    exit;
}

$stmtExiste->bind_param("i", $id_usuario);
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
    header("Location: ../../public/admin_usuarios.php?error=no_existe" . $redirect_suffix);
    exit;
}

$sqlUpdate = "UPDATE usuarios SET rol = ?, activo = ? WHERE id_usuario = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
    header("Location: ../../public/admin_usuarios.php?error=1" . $redirect_suffix);
    exit;
}

$stmtUpdate->bind_param("sii", $rol, $activo_int, $id_usuario);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
    header("Location: ../../public/admin_usuarios.php?error=1" . $redirect_suffix);
    exit;
}

$stmtUpdate->close();
header("Location: ../../public/admin_usuarios.php?ok=actualizado" . $redirect_suffix);
exit;

