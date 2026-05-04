<?php
session_start();

if (!isset($_SESSION["usuario"]) || $_SESSION["rol"] !== "entrenador") {
    header("Location: /WHITEGYM/php/login.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área Entrenador</title>
</head>
<body>

<h1>Área de Entrenador</h1>
<p>Bienvenido <?php echo $_SESSION["usuario"]; ?></p>

<a href="logout.php">Cerrar sesión</a>

</body>
</html>
