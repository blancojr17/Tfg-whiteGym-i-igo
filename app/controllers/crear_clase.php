<?php
// creacion de nuevas clases
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
$nombre = trim($_POST["nombre"] ?? "");
// recogida de datos del formulario
$descripcion = trim($_POST["descripcion"] ?? "");
// recogida de datos del formulario
$fecha = trim($_POST["fecha"] ?? "");
// recogida de datos del formulario
$capacidad = (int) ($_POST["capacidad"] ?? 0);

if ($nombre === "") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=nombre");
    exit;
}

if ($descripcion === "") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=descripcion");
    exit;
}

if ($fecha === "") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=fecha");
    exit;
}

if ($capacidad <= 0) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=capacidad");
    exit;
}

$fecha_sql = str_replace("T", " ", $fecha);

// consulta sql
$sql = "INSERT INTO clases (nombre, descripcion, fecha, capacidad, id_entrenador)
        VALUES (?, ?, ?, ?, ?)";
// preparacion de la consulta
$stmt = $conexion->prepare($sql);

if (!$stmt) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmt->bind_param("sssii", $nombre, $descripcion, $fecha_sql, $capacidad, $id_entrenador);

if (!$stmt->execute()) {
    $stmt->close();
// redireccion final
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

$stmt->close();
// redireccion final
header("Location: ../../public/entrenador.php?ok=creada");
exit;

