<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "admin") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$id_clase = (int) ($_POST["id_clase"] ?? 0);

if ($id_clase <= 0) {
    header("Location: ../../public/admin.php?error=admin_clase_id");
    exit;
}

$sqlExiste = "SELECT id_clase FROM clases WHERE id_clase = ? LIMIT 1";
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtExiste->bind_param("i", $id_clase);
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
    header("Location: ../../public/admin.php?error=admin_clase_no_existe");
    exit;
}

$conexion->begin_transaction();

try {
    $sqlDeleteReservas = "DELETE FROM usuarios_clases WHERE id_clase = ?";
    $stmtDeleteReservas = $conexion->prepare($sqlDeleteReservas);

    if (!$stmtDeleteReservas) {
        throw new Exception("reservas");
    }

    $stmtDeleteReservas->bind_param("i", $id_clase);

    if (!$stmtDeleteReservas->execute()) {
        throw new Exception("reservas_exec");
    }

    $stmtDeleteReservas->close();

    $sqlDeleteClase = "DELETE FROM clases WHERE id_clase = ? LIMIT 1";
    $stmtDeleteClase = $conexion->prepare($sqlDeleteClase);

    if (!$stmtDeleteClase) {
        throw new Exception("clase");
    }

    $stmtDeleteClase->bind_param("i", $id_clase);

    if (!$stmtDeleteClase->execute()) {
        throw new Exception("clase_exec");
    }

    if ($stmtDeleteClase->affected_rows !== 1) {
        $stmtDeleteClase->close();
        $conexion->rollback();
        header("Location: ../../public/admin.php?error=admin_clase_no_existe");
        exit;
    }

    $stmtDeleteClase->close();

    $conexion->commit();
    header("Location: ../../public/admin.php?ok=admin_clase_eliminada");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
    header("Location: ../../public/admin.php?error=1");
    exit;
}
