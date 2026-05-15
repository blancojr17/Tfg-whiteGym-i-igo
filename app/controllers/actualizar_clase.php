<?php
// actualizacion de datos de una clase
// inicio de sesión y conexión con la base de datos
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../../config/conexion.php";

// comprobación de acceso solo para entrenadores
// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "entrenador") {
// redireccion final
    header("Location: ../../public/login.php");
    exit;
}

// comprobación de envío mediante método post
// validacion del metodo recibido
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=metodo");
    exit;
}

// recogida y limpieza de datos enviados desde el formulario
$id_entrenador = (int) $_SESSION["id_usuario"];
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

// validación de id de clase
if ($id_clase <= 0) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=clase");
    exit;
}

// validación del nombre
if ($nombre === "") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=nombre");
    exit;
}

// validación de descripción
if ($descripcion === "") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=descripcion");
    exit;
}

// validación de fecha
if ($fecha === "") {
// redireccion final
    header("Location: ../../public/entrenador.php?error=fecha");
    exit;
}

// validación de capacidad
if ($capacidad <= 0) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=capacidad");
    exit;
}

// adaptación de formato fecha para mysql
$fecha_sql = str_replace("T", " ", $fecha);

// actualización de datos de la clase
// consulta sql
$sql = "UPDATE clases
        SET nombre = ?, descripcion = ?, fecha = ?, capacidad = ?
        WHERE id_clase = ? AND id_entrenador = ?";
// preparacion de la consulta
$stmt = $conexion->prepare($sql);

// comprobación de preparación sql
if (!$stmt) {
// redireccion final
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

// asociación de parámetros
$stmt->bind_param("sssiii", $nombre, $descripcion, $fecha_sql, $capacidad, $id_clase, $id_entrenador);

// ejecución de consulta
if (!$stmt->execute()) {
    $stmt->close();
// redireccion final
    header("Location: ../../public/entrenador.php?error=1");
    exit;
}

// comprobación de modificaciones realizadas
// comprobacion de la consulta
if ($stmt->affected_rows === 0) {
    $stmt->close();

    // comprobación de existencia de la clase
// consulta sql
    $sqlExiste = "SELECT id_clase FROM clases WHERE id_clase = ? LIMIT 1";
// preparacion de la consulta
    $stmtExiste = $conexion->prepare($sqlExiste);

    if (!$stmtExiste) {
// redireccion final
        header("Location: ../../public/entrenador.php?error=1");
        exit;
    }

    $stmtExiste->bind_param("i", $id_clase);
// ejecucion de la consulta
    $stmtExiste->execute();
    $resExiste = $stmtExiste->get_result();
    $existe = $resExiste && $resExiste->num_rows === 1;
    $stmtExiste->close();

    // comprobación de clase existente
    if (!$existe) {
// redireccion final
        header("Location: ../../public/entrenador.php?error=clase");
        exit;
    }

    // comprobación de permisos del entrenador sobre la clase
// consulta sql
    $sqlPropia = "SELECT id_clase FROM clases WHERE id_clase = ? AND id_entrenador = ? LIMIT 1";
// preparacion de la consulta
    $stmtPropia = $conexion->prepare($sqlPropia);

    if (!$stmtPropia) {
// redireccion final
        header("Location: ../../public/entrenador.php?error=1");
        exit;
    }

    $stmtPropia->bind_param("ii", $id_clase, $id_entrenador);
// ejecucion de la consulta
    $stmtPropia->execute();
    $resPropia = $stmtPropia->get_result();
    $propia = $resPropia && $resPropia->num_rows === 1;
    $stmtPropia->close();

    // validación final de permisos
    if (!$propia) {
// redireccion final
        header("Location: ../../public/entrenador.php?error=permiso");
        exit;
    }
}

// cierre de consulta y redirección final
$stmt->close();
// redireccion final
header("Location: ../../public/entrenador.php?ok=actualizada");
exit;
