<?php
// envio de sugerencias del usuario
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/index.php#contacto");
    exit;
}

// recogida de datos del formulario
$nombre = trim((string) ($_POST["nombre"] ?? ""));
// recogida de datos del formulario
$email = trim((string) ($_POST["email"] ?? ""));
// recogida de datos del formulario
$telefono = trim((string) ($_POST["telefono"] ?? ""));
// recogida de datos del formulario
$asunto = trim((string) ($_POST["asunto"] ?? ""));
// recogida de datos del formulario
$mensaje = trim((string) ($_POST["mensaje"] ?? ""));

$asuntos_validos = [
    "informacion" => "Informacion",
    "clases" => "Clases",
    "membresias" => "Membresias"
];

if ($nombre === "" || $email === "" || $mensaje === "") {
// redireccion final
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
// redireccion final
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

if (strlen($nombre) > 120 || strlen($email) > 150 || strlen($mensaje) > 2000 || strlen($telefono) > 40) {
// redireccion final
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

// mensajes segun el resultado
$mensaje_guardado = $mensaje;
if (!empty($lineas_extra)) {
// mensajes segun el resultado
    $mensaje_guardado = implode(" | ", $lineas_extra) . "\n" . $mensaje;
}

// consulta sql
$sql = "INSERT INTO sugerencias (nombre, email, mensaje, fecha, estado)
        VALUES (?, ?, ?, NOW(), 'pendiente')";
// preparacion de la consulta
$stmt = $conexion->prepare($sql);

if (!$stmt) {
// redireccion final
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

$stmt->bind_param("sss", $nombre, $email, $mensaje_guardado);

if (!$stmt->execute()) {
    $stmt->close();
// redireccion final
    header("Location: ../../public/index.php?sugerencia=error#contacto");
    exit;
}

$stmt->close();
// redireccion final
header("Location: ../../public/index.php?sugerencia=ok#contacto");
exit;

