<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$id_clase = (int) ($_POST["id_clase"] ?? 0);
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$capacidad = (int) ($_POST["capacidad"] ?? 0);

if ($id_clase <= 0) {
    header("Location: ../../public/admin_clases.php?error=admin_clase_id");
    exit;
}

if ($nombre === "") {
    header("Location: ../../public/admin_clases.php?error=admin_clase_nombre");
    exit;
}

if ($fecha === "") {
    header("Location: ../../public/admin_clases.php?error=admin_clase_fecha");
    exit;
}

if ($capacidad <= 0) {
    header("Location: ../../public/admin_clases.php?error=admin_clase_capacidad");
    exit;
}

$sqlExiste = "SELECT id_clase FROM clases WHERE id_clase = ? LIMIT 1";
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$stmtExiste->bind_param("i", $id_clase);
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
    header("Location: ../../public/admin_clases.php?error=admin_clase_no_existe");
    exit;
}

$fecha_sql = str_replace("T", " ", $fecha);

$sqlUpdate = "UPDATE clases
              SET nombre = ?, descripcion = ?, fecha = ?, capacidad = ?
              WHERE id_clase = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$stmtUpdate->bind_param("sssii", $nombre, $descripcion, $fecha_sql, $capacidad, $id_clase);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$stmtUpdate->close();
header("Location: ../../public/admin_clases.php?ok=admin_clase_actualizada");
exit;

