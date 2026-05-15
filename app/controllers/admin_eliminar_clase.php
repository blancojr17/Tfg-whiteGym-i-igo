<?php
// eliminacion de clases desde el panel admin
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
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

// recogida de datos del formulario
$id_clase = (int) ($_POST["id_clase"] ?? 0);

if ($id_clase <= 0) {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=admin_clase_id");
    exit;
}

// consulta sql
$sqlExiste = "SELECT id_clase FROM clases WHERE id_clase = ? LIMIT 1";
// preparacion de la consulta
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$stmtExiste->bind_param("i", $id_clase);
// ejecucion de la consulta
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=admin_clase_no_existe");
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
    $sqlDeleteClase = "DELETE FROM clases WHERE id_clase = ? LIMIT 1";
// preparacion de la consulta
    $stmtDeleteClase = $conexion->prepare($sqlDeleteClase);

    if (!$stmtDeleteClase) {
        throw new Exception("clase");
    }

    $stmtDeleteClase->bind_param("i", $id_clase);

    if (!$stmtDeleteClase->execute()) {
        throw new Exception("clase_exec");
    }

// comprobacion de la consulta
    if ($stmtDeleteClase->affected_rows !== 1) {
        $stmtDeleteClase->close();
        $conexion->rollback();
// redireccion final
        header("Location: ../../public/admin_clases.php?error=admin_clase_no_existe");
        exit;
    }

    $stmtDeleteClase->close();

    $conexion->commit();
// redireccion final
    header("Location: ../../public/admin_clases.php?ok=admin_clase_eliminada");
    exit;
} catch (Exception $e) {
    $conexion->rollback();
// redireccion final
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}


