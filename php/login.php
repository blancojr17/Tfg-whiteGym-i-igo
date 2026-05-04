<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../acceso.css">
</head>
<body class="pagina-acceso">

<div class="contenedor-acceso">

    <a href="../index.html" class="logo-acceso">
        <img src="../assets/img/logosin.png" alt="Logo WhiteGym">
    </a>

    <h1>Iniciar sesión</h1>

    <form class="formulario-acceso" action="procesar_login.php" method="post">

        <input type="email" name="email" placeholder="Correo electrónico" required>
     <input type="password" name="password" placeholder="Contraseña" required>

        <button type="submit">Entrar</button>
    </form>

    <?php
if (isset($_GET["error"])) {
    echo "<p style='color:red'>Usuario o contraseña incorrectos</p>";
}
?>


    <p class="enlace-acceso">
        ¿No tienes cuenta?
        <a href="/WHITEGYM/php/registro.php">Regístrate</a>
    </p>

</div>

</body>
</html>
