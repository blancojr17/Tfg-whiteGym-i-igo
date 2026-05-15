<?php
// gestion de planes del gimnasio
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
    header("Location: ../../public/admin_planes.php?error=1");
    exit;
}

// recogida de datos del formulario
$accion = trim($_POST["accion"] ?? "");

$resColumna = $conexion->query("SHOW COLUMNS FROM planes LIKE 'activo'");
if (!$resColumna || $resColumna->num_rows === 0) {
    if (!$conexion->query("ALTER TABLE planes ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1")) {
// redireccion final
        header("Location: ../../public/admin_planes.php?error=plan_activo_columna");
        exit;
    }
}

if ($accion !== "crear" && $accion !== "editar") {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=1");
    exit;
}

// recogida de datos del formulario
$nombre = trim($_POST["nombre"] ?? "");
// recogida de datos del formulario
$precio = $_POST["precio"] ?? "";
// recogida de datos del formulario
$tipo = trim($_POST["tipo"] ?? "");
// recogida de datos del formulario
$duracion_dias = (int) ($_POST["duracion_dias"] ?? 0);
// recogida de datos del formulario
$usos = (int) ($_POST["usos"] ?? 0);
// recogida de datos del formulario
$activo = $_POST["activo"] ?? "1";

if ($nombre === "") {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_nombre");
    exit;
}

if (!is_numeric($precio) || (float) $precio < 0) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_precio");
    exit;
}

if ($tipo !== "suscripcion" && $tipo !== "bono") {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_tipo");
    exit;
}

if ($tipo === "suscripcion" && $duracion_dias <= 0) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_duracion");
    exit;
}

if ($tipo === "bono" && $usos <= 0) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_usos");
    exit;
}

if ($activo !== "0" && $activo !== "1") {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=activo");
    exit;
}

$precio_float = (float) $precio;
$activo_int = (int) $activo;

if ($tipo === "suscripcion") {
    $usos = 0;
}

if ($tipo === "bono") {
    $duracion_dias = max(1, $duracion_dias);
}

if ($accion === "crear") {
// consulta sql
    $sql = "INSERT INTO planes (nombre, precio, tipo, duracion_dias, usos, activo)
            VALUES (?, ?, ?, ?, ?, ?)";
// preparacion de la consulta
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
// redireccion final
        header("Location: ../../public/admin_planes.php?error=1");
        exit;
    }

    $stmt->bind_param("sdsiii", $nombre, $precio_float, $tipo, $duracion_dias, $usos, $activo_int);

    if (!$stmt->execute()) {
        $stmt->close();
// redireccion final
        header("Location: ../../public/admin_planes.php?error=1");
        exit;
    }

    $stmt->close();
// redireccion final
    header("Location: ../../public/admin_planes.php?ok=creado_plan");
    exit;
}

// recogida de datos del formulario
$id_plan = (int) ($_POST["id_plan"] ?? 0);
if ($id_plan <= 0) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_id");
    exit;
}

// consulta sql
$sqlExiste = "SELECT id_plan FROM planes WHERE id_plan = ? LIMIT 1";
// preparacion de la consulta
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=1");
    exit;
}

$stmtExiste->bind_param("i", $id_plan);
// ejecucion de la consulta
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=plan_no_existe");
    exit;
}

// consulta sql
$sqlUpdate = "UPDATE planes
              SET nombre = ?, precio = ?, tipo = ?, duracion_dias = ?, usos = ?, activo = ?
              WHERE id_plan = ?";
// preparacion de la consulta
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
// redireccion final
    header("Location: ../../public/admin_planes.php?error=1");
    exit;
}

$stmtUpdate->bind_param("sdsiiii", $nombre, $precio_float, $tipo, $duracion_dias, $usos, $activo_int, $id_plan);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
// redireccion final
    header("Location: ../../public/admin_planes.php?error=1");
    exit;
}

$stmtUpdate->close();
// redireccion final
header("Location: ../../public/admin_planes.php?ok=actualizado_plan");
exit;


