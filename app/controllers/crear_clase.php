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
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$capacidad = (int) ($_POST["capacidad"] ?? 0);

if ($nombre === "") {
    header("Location: ../../public/entrenador.php?error=nombre");
    exit;
}

if ($descripcion === "") {
    header("Location: ../../public/entrenador.php?error=descripcion");
    exit;
}

if ($fecha === "") {
    header("Location: ../../public/entrenador.php?error=fecha");
    exit;
}

if ($capacidad <= 0) {
    header("Location: ../../public/entrenador.php?error=capacidad");
    exit;
}

$fecha_sql = str_replace("T", " ", $fecha);

$sql = "INSERT INTO clases (nombre, descripcion, fecha, capacidad, id_entrenador)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmt->bind_param("sssii", $nombre, $descripcion, $fecha_sql, $capacidad, $id_entrenador);

if (!$stmt->execute()) {
    $stmt->close();
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmt->close();
header("Location: ../../public/entrenador.php?ok=creada");
exit;
