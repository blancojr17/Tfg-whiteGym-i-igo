<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "entrenador") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/entrenador.php?error=metodo");
    exit;
}

$id_entrenador = (int) $_SESSION["id_usuario"];
$id_clase = (int) ($_POST["id_clase"] ?? 0);

if ($id_clase <= 0) {
    header("Location: ../../public/entrenador.php?error=clase");
    exit;
}

$sqlPropia = "SELECT id_clase FROM clases WHERE id_clase = ? AND id_entrenador = ? LIMIT 1";
$stmtPropia = $conexion->prepare($sqlPropia);

if (!$stmtPropia) {
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmtPropia->bind_param("ii", $id_clase, $id_entrenador);
$stmtPropia->execute();
$resPropia = $stmtPropia->get_result();
$esPropia = $resPropia && $resPropia->num_rows === 1;
$stmtPropia->close();

if (!$esPropia) {
    header("Location: ../../public/entrenador.php?error=permiso");
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

    $sqlDeleteClase = "DELETE FROM clases WHERE id_clase = ? AND id_entrenador = ? LIMIT 1";
    $stmtDeleteClase = $conexion->prepare($sqlDeleteClase);

    if (!$stmtDeleteClase) {
        throw new Exception("clase");
    }

    $stmtDeleteClase->bind_param("ii", $id_clase, $id_entrenador);

    if (!$stmtDeleteClase->execute()) {
        throw new Exception("clase_exec");
    }

    if ($stmtDeleteClase->affected_rows !== 1) {
        $stmtDeleteClase->close();
        $conexion->rollback();
        header("Location: ../../public/entrenador.php?error=permiso");
        exit;
    }

    $stmtDeleteClase->close();

    $conexion->commit();
    header("Location: ../../public/entrenador.php?ok=eliminada");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}
