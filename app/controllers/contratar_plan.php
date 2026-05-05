<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: ../../public/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/planes.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$id_plan = (int) ($_POST["id_plan"] ?? 0);

if ($id_plan <= 0) {
    header("Location: ../../public/planes.php?error=datos");
    exit;
}

$sql = "SELECT * FROM planes WHERE id_plan = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/planes.php?error=1");
    exit;
}

$stmt->bind_param("i", $id_plan);
$stmt->execute();
$resultado = $stmt->get_result();
$plan = $resultado ? $resultado->fetch_assoc() : null;

if (!$plan) {
    header("Location: ../../public/planes.php?error=plan");
    exit;
}

$tipo = $plan["tipo"] ?? "";
$fecha_inicio = date("Y-m-d");

if ($tipo === "suscripcion") {
    $duracion_dias = (int) ($plan["duracion_dias"] ?? 0);

    if ($duracion_dias <= 0) {
        header("Location: ../../public/planes.php?error=duracion");
        exit;
    }

    $fecha_fin = date("Y-m-d", strtotime("+" . $duracion_dias . " days"));

    $sql = "INSERT INTO usuarios_planes (id_usuario, id_plan, fecha_inicio, fecha_fin, usos_restantes)
            VALUES (?, ?, ?, ?, NULL)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        header("Location: ../../public/planes.php?error=1");
        exit;
    }

    $stmt->bind_param("iiss", $id_usuario, $id_plan, $fecha_inicio, $fecha_fin);

    if (!$stmt->execute()) {
        header("Location: ../../public/planes.php?error=1");
        exit;
    }
} elseif ($tipo === "bono") {
    $usos_plan = (int) ($plan["usos"] ?? 0);
    $duracion_dias = (int) ($plan["duracion_dias"] ?? 0);

    if ($usos_plan <= 0) {
        header("Location: ../../public/planes.php?error=usos");
        exit;
    }

    if ($duracion_dias <= 0) {
        header("Location: ../../public/planes.php?error=duracion");
        exit;
    }

    $fecha_fin = date("Y-m-d", strtotime("+" . $duracion_dias . " days"));

    $sql = "INSERT INTO usuarios_planes (id_usuario, id_plan, fecha_inicio, fecha_fin, usos_restantes)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        header("Location: ../../public/planes.php?error=1");
        exit;
    }

    $stmt->bind_param("iissi", $id_usuario, $id_plan, $fecha_inicio, $fecha_fin, $usos_plan);

    if (!$stmt->execute()) {
        header("Location: ../../public/planes.php?error=1");
        exit;
    }
} else {
    header("Location: ../../public/planes.php?error=tipo");
    exit;
}

header("Location: ../../public/planes.php?ok=1");
exit;
