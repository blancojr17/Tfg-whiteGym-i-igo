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

$accion = trim($_POST["accion"] ?? "");

$resColumna = $conexion->query("SHOW COLUMNS FROM planes LIKE 'activo'");
if (!$resColumna || $resColumna->num_rows === 0) {
    if (!$conexion->query("ALTER TABLE planes ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1")) {
        header("Location: ../../public/admin.php?error=plan_activo_columna");
        exit;
    }
}

if ($accion !== "crear" && $accion !== "editar") {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$nombre = trim($_POST["nombre"] ?? "");
$precio = $_POST["precio"] ?? "";
$tipo = trim($_POST["tipo"] ?? "");
$duracion_dias = (int) ($_POST["duracion_dias"] ?? 0);
$usos = (int) ($_POST["usos"] ?? 0);
$activo = $_POST["activo"] ?? "1";

if ($nombre === "") {
    header("Location: ../../public/admin.php?error=plan_nombre");
    exit;
}

if (!is_numeric($precio) || (float) $precio < 0) {
    header("Location: ../../public/admin.php?error=plan_precio");
    exit;
}

if ($tipo !== "suscripcion" && $tipo !== "bono") {
    header("Location: ../../public/admin.php?error=plan_tipo");
    exit;
}

if ($tipo === "suscripcion" && $duracion_dias <= 0) {
    header("Location: ../../public/admin.php?error=plan_duracion");
    exit;
}

if ($tipo === "bono" && $usos <= 0) {
    header("Location: ../../public/admin.php?error=plan_usos");
    exit;
}

if ($activo !== "0" && $activo !== "1") {
    header("Location: ../../public/admin.php?error=activo");
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
    $sql = "INSERT INTO planes (nombre, precio, tipo, duracion_dias, usos, activo)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        header("Location: ../../public/admin.php?error=1");
        exit;
    }

    $stmt->bind_param("sdsiii", $nombre, $precio_float, $tipo, $duracion_dias, $usos, $activo_int);

    if (!$stmt->execute()) {
        $stmt->close();
        header("Location: ../../public/admin.php?error=1");
        exit;
    }

    $stmt->close();
    header("Location: ../../public/admin.php?ok=creado_plan");
    exit;
}

$id_plan = (int) ($_POST["id_plan"] ?? 0);
if ($id_plan <= 0) {
    header("Location: ../../public/admin.php?error=plan_id");
    exit;
}

$sqlExiste = "SELECT id_plan FROM planes WHERE id_plan = ? LIMIT 1";
$stmtExiste = $conexion->prepare($sqlExiste);

if (!$stmtExiste) {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtExiste->bind_param("i", $id_plan);
$stmtExiste->execute();
$resExiste = $stmtExiste->get_result();
$existe = $resExiste && $resExiste->num_rows === 1;
$stmtExiste->close();

if (!$existe) {
    header("Location: ../../public/admin.php?error=plan_no_existe");
    exit;
}

$sqlUpdate = "UPDATE planes
              SET nombre = ?, precio = ?, tipo = ?, duracion_dias = ?, usos = ?, activo = ?
              WHERE id_plan = ?";
$stmtUpdate = $conexion->prepare($sqlUpdate);

if (!$stmtUpdate) {
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtUpdate->bind_param("sdsiiii", $nombre, $precio_float, $tipo, $duracion_dias, $usos, $activo_int, $id_plan);

if (!$stmtUpdate->execute()) {
    $stmtUpdate->close();
    header("Location: ../../public/admin.php?error=1");
    exit;
}

$stmtUpdate->close();
header("Location: ../../public/admin.php?ok=actualizado_plan");
exit;
