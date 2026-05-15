<?php
// actualizacion de clases desde el panel admin
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
// recogida de datos del formulario
$nombre = trim($_POST["nombre"] ?? "");
// recogida de datos del formulario
$descripcion = trim($_POST["descripcion"] ?? "");
// recogida de datos del formulario
$fecha = trim($_POST["fecha"] ?? "");
// recogida de datos del formulario
$capacidad = (int) ($_POST["capacidad"] ?? 0);

if ($id_clase <= 0) {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=admin_clase_id");
    exit;
}

if ($nombre === "") {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=admin_clase_nombre");
    exit;
}

if ($fecha === "") {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=admin_clase_fecha");
    exit;
}

if ($capacidad <= 0) {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=admin_clase_capacidad");
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

$fecha_sql = str_replace("T", " ", $fecha);

// consulta sql
$sqlUpdate = "UPDATE clases
              SET nombre = ?, descripcion = ?, fecha = ?, capacidad = ?
              WHERE id_clase = ?";
// preparacion de la consulta
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
// redireccion final
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$stmtUpdate->bind_param("sssii", $nombre, $descripcion, $fecha_sql, $capacidad, $id_clase);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
// redireccion final
    header("Location: ../../public/admin_clases.php?error=1");
    exit;
}

$stmtUpdate->close();
// redireccion final
header("Location: ../../public/admin_clases.php?ok=admin_clase_actualizada");
exit;


