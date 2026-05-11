<?php
require_once __DIR__ . "/../../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/index.php#contacto");
    exit;
}

$nombre = trim((string) ($_POST["nombre"] ?? ""));
$email = trim((string) ($_POST["email"] ?? ""));
$telefono = trim((string) ($_POST["telefono"] ?? ""));
$asunto = trim((string) ($_POST["asunto"] ?? ""));
$mensaje = trim((string) ($_POST["mensaje"] ?? ""));

$asuntos_validos = [
    "informacion" => "Informacion",
    "clases" => "Clases",
    "membresias" => "Membresias"
];

if ($nombre === "" || $email === "" || $mensaje === "") {
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

if (strlen($nombre) > 120 || strlen($email) > 150 || strlen($mensaje) > 2000 || strlen($telefono) > 40) {
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

$lineas_extra = [];

if ($asunto !== "" && isset($asuntos_validos[$asunto])) {
    $lineas_extra[] = "Asunto: " . $asuntos_validos[$asunto];
}

if ($telefono !== "") {
    $lineas_extra[] = "Telefono: " . $telefono;
}

$mensaje_guardado = $mensaje;
if (!empty($lineas_extra)) {
    $mensaje_guardado = implode(" | ", $lineas_extra) . "\n" . $mensaje;
}

$sql = "INSERT INTO sugerencias (nombre, email, mensaje, fecha, estado)
        VALUES (?, ?, ?, NOW(), 'pendiente')";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

$stmt->bind_param("sss", $nombre, $email, $mensaje_guardado);

if (!$stmt->execute()) {
    $stmt->close();
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

$stmt->close();
header("Location: ../../public/index.php?sugerencia=ok#contacto");
exit;
