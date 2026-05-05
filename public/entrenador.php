<?php
session_start();

if (!isset($_SESSION["email"]) || $_SESSION["rol"] !== "entrenador") {
    header("Location: login.php");
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
<p>Bienvenido <?php echo $_SESSION["nombre"]; ?></p>

<a href="../app/controllers/logout.php">Cerrar sesión</a>

</body>
</html>
