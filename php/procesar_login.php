<?php
session_start();
require __DIR__ . "/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /WHITEGYM/php/login.php");
    exit;
}

$email = trim($_POST["email"]);
$password = $_POST["password"];

$sql = "select id, nombre, email, password, rol, activo
        from usuarios
        where email = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {

    $fila = $resultado->fetch_assoc();

    if ($fila["activo"] == 1 && password_verify($password, $fila["password"])) {

        $_SESSION["id_usuario"] = $fila["id"];
        $_SESSION["nombre"] = $fila["nombre"];
        $_SESSION["email"] = $fila["email"];
        $_SESSION["rol"] = $fila["rol"];

        if ($fila["rol"] === "admin") {
    header("Location: /WHITEGYM/php/admin.php");
    } else {
    header("Location: /WHITEGYM/php/cliente.php");
    }
exit;

    }
}


header("Location: /WHITEGYM/php/login.php?error=1");
exit;
