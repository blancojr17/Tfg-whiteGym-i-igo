<?php
// eliminacion de clases
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "entrenador") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=metodo");
    exit;
}

$id_entrenador = (int) $_SESSION["id_usuario"];
// recogida de datos del formulario
$id_clase = (int) ($_POST["id_clase"] ?? 0);

if ($id_clase <= 0) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=clase");
    exit;
}

// consulta sql
$sqlPropia = "SELECT id_clase FROM clases WHERE id_clase = ? AND id_entrenador = ? LIMIT 1";
// preparacion de la consulta
$stmtPropia = $conexion->prepare($sqlPropia);

if (!$stmtPropia) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmtPropia->bind_param("ii", $id_clase, $id_entrenador);
// ejecucion de la consulta
$stmtPropia->execute();
$resPropia = $stmtPropia->get_result();
$esPropia = $resPropia && $resPropia->num_rows === 1;
$stmtPropia->close();

if (!$esPropia) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=permiso");
    exit;
}

$conexion->begin_transaction();

try {
// consulta sql
    $sqlDeleteReservas = "DELETE FROM usuarios_clases WHERE id_clase = ?";
// preparacion de la consulta
    $stmtDeleteReservas = $conexion->prepare($sqlDeleteReservas);

    if (!$stmtDeleteReservas) {
        throw new Exception("reservas");
    }

    $stmtDeleteReservas->bind_param("i", $id_clase);

    if (!$stmtDeleteReservas->execute()) {
        throw new Exception("reservas_exec");
    }

    $stmtDeleteReservas->close();

// consulta sql
    $sqlDeleteClase = "DELETE FROM clases WHERE id_clase = ? AND id_entrenador = ? LIMIT 1";
// preparacion de la consulta
    $stmtDeleteClase = $conexion->prepare($sqlDeleteClase);

    if (!$stmtDeleteClase) {
        throw new Exception("clase");
    }

    $stmtDeleteClase->bind_param("ii", $id_clase, $id_entrenador);

    if (!$stmtDeleteClase->execute()) {
        throw new Exception("clase_exec");
    }

// comprobacion de la consulta
    if ($stmtDeleteClase->affected_rows !== 1) {
        $stmtDeleteClase->close();
        $conexion->rollback();
// redireccion final
        header("Location: ../../public/entrenador.php?error=permiso");
        exit;
    }

    $stmtDeleteClase->close();

    $conexion->commit();
// redireccion final
    header("Location: ../../public/entrenador.php?ok=eliminada");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
// redireccion final
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

