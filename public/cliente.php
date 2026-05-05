<?php
session_start();

if (!isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área Cliente - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/cliente.css">
</head>
<body>

<header id="cabecera-cliente">
    <h1>WhiteGym</h1>
    <div id="info-cliente">
        <span><?php echo $_SESSION["nombre"]; ?></span>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-cliente">
    <h2>Bienvenido a tu área personal</h2>
    <p><a href="planes.php">Ver planes disponibles</a></p>
</main>

</body>
</html>
