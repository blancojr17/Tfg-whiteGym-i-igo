<?php
session_start();

if (isset($_SESSION["id_usuario"], $_SESSION["rol"])) {
    if ($_SESSION["rol"] === "admin") {
        header("Location: admin.php");
        exit;
    }

    if ($_SESSION["rol"] === "entrenador") {
        header("Location: entrenador.php");
        exit;
    }

    header("Location: cliente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/acceso.css">
</head>
<body class="pagina-acceso">

<div class="contenedor-acceso">

    <a href="index.php" class="logo-acceso">
        <img src="assets/img/logosin.png" alt="Logo WhiteGym">
    </a>

    <h1>Iniciar sesión</h1>

    <form class="formulario-acceso" action="../app/controllers/procesar_login.php" method="post">
        <input type="email" name="email" placeholder="Correo electrónico" required autocomplete="email">
        <input type="password" name="password" placeholder="Contraseña" required autocomplete="current-password">

        <button type="submit">Entrar</button>
    </form>

    <?php if (isset($_GET["error"]) && $_GET["error"] === "campos"): ?>
        <p style="color:red">Debes completar todos los campos correctamente.</p>
    <?php elseif (isset($_GET["error"])): ?>
        <p style="color:red">Usuario o contraseña incorrectos.</p>
    <?php endif; ?>

    <?php if (isset($_GET["registro"]) && $_GET["registro"] === "ok"): ?>
        <p style="color:green">Registro completado. Ya puedes iniciar sesión.</p>
    <?php endif; ?>

    <p class="enlace-acceso">
        ¿No tienes cuenta?
        <a href="registro.php">Regístrate</a>
    </p>

</div>

</body>
</html>
