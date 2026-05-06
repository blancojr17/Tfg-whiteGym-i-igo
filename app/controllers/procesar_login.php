<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../../public/login.php?error=campos");
    exit;
}

$sql = "select id_usuario, nombre, email, password, rol, activo from usuarios where email = ?";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: ../../public/login.php?error=1");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows === 1) {
    $fila = $resultado->fetch_assoc();

    if ($fila["activo"] == 1 && password_verify($password, $fila["password"])) {
        session_regenerate_id(true);

        $_SESSION["id_usuario"] = $fila["id_usuario"];
        $_SESSION["nombre"] = $fila["nombre"];
        $_SESSION["email"] = $fila["email"];
        $_SESSION["rol"] = $fila["rol"];

        if ($fila["rol"] === "admin") {
            header("Location: ../../public/admin.php");
        } elseif ($fila["rol"] === "entrenador") {
            header("Location: ../../public/entrenador.php");
        } else {
            header("Location: ../../public/cliente.php");
        }
        exit;
    }
}

header("Location: ../../public/login.php?error=1");
exit;
