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
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$fecha = trim($_POST["fecha"] ?? "");
$capacidad = (int) ($_POST["capacidad"] ?? 0);

if ($id_clase <= 0) {
    header("Location: ../../public/entrenador.php?error=clase");
    exit;
}

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

$sql = "UPDATE clases
        SET nombre = ?, descripcion = ?, fecha = ?, capacidad = ?
        WHERE id_clase = ? AND id_entrenador = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmt->bind_param("sssiii", $nombre, $descripcion, $fecha_sql, $capacidad, $id_clase, $id_entrenador);

if (!$stmt->execute()) {
    $stmt->close();
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

if ($stmt->affected_rows === 0) {
    $stmt->close();

    $sqlExiste = "SELECT id_clase FROM clases WHERE id_clase = ? LIMIT 1";
    $stmtExiste = $conexion->prepare($sqlExiste);

    if (!$stmtExiste) {
        header("Location: ../../public/entrenador.php?error=1");
        exit;
    }

    $stmtExiste->bind_param("i", $id_clase);
    $stmtExiste->execute();
    $resExiste = $stmtExiste->get_result();
    $existe = $resExiste && $resExiste->num_rows === 1;
    $stmtExiste->close();

    if (!$existe) {
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
    $propia = $resPropia && $resPropia->num_rows === 1;
    $stmtPropia->close();

    if (!$propia) {
        header("Location: ../../public/entrenador.php?error=permiso");
        exit;
    }
}

$stmt->close();
header("Location: ../../public/entrenador.php?ok=actualizada");
exit;
