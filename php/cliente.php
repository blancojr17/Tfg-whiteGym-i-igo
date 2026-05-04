<?php
session_start();

if (!isset($_SESSION["email"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: /WHITEGYM/php/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área Cliente - WhiteGym</title>
    <link rel="stylesheet" href="../cliente.css">
</head>
<body>

<header id="cabecera-cliente">
    <h1>WhiteGym</h1>
    <div id="info-cliente">
        <span><?php echo $_SESSION["nombre"]; ?></span>
        <a href="logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-cliente">
    <h2>Bienvenido a tu área personal</h2>
</main>

</body>
</html>