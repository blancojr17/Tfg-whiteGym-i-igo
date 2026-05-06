<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/admin.php?error=metodo");
    exit;
}

$id_admin_actual = (int) $_SESSION["id_usuario"];
$id_usuario = (int) ($_POST["id_usuario"] ?? 0);
$rol = trim($_POST["rol"] ?? "");
$activo = $_POST["activo"] ?? "";

$roles_validos = ["usuario", "entrenador", "admin"];

if ($id_usuario <= 0) {
    header("Location: ../../public/admin.php?error=id");
    exit;
}

if (!in_array($rol, $roles_validos, true)) {
    header("Location: ../../public/admin.php?error=rol");
    exit;
}

if ($activo !== "0" && $activo !== "1") {
    header("Location: ../../public/admin.php?error=activo");
    exit;
}

$activo_int = (int) $activo;

if ($id_usuario === $id_admin_actual && ($rol !== "admin" || $activo_int !== 1)) {
    header("Location: ../../public/admin.php?error=autoproteccion");
    exit;
}

$sqlExiste = "SELECT id_usuario FROM usuarios WHERE id_usuario = ? LIMIT 1";
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtExiste->bind_param("i", $id_usuario);
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
    header("Location: ../../public/admin.php?error=no_existe");
    exit;
}

$sqlUpdate = "UPDATE usuarios SET rol = ?, activo = ? WHERE id_usuario = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtUpdate->bind_param("sii", $rol, $activo_int, $id_usuario);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtUpdate->close();
header("Location: ../../public/admin.php?ok=actualizado");
exit;
