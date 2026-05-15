<?php
// pantalla de registro de usuarios
// inicio de sesion
session_start();

if (isset($_SESSION["id_usuario"], $_SESSION["rol"])) {
    if ($_SESSION["rol"] === "admin") {
// redireccion final
        header("Location: admin.php");
        exit;
    }

    if ($_SESSION["rol"] === "entrenador") {
// redireccion final
        header("Location: entrenador.php");
        exit;
    }

// redireccion final
    header("Location: cliente.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="assets/css/acceso.css">
</head>
<body class="pagina-acceso">

    <div class="contenedor-acceso contenedor-acceso-registro">
        <a href="index.php" class="logo-acceso">
            <img src="assets/img/logosin.png" alt="Logo WhiteGym">
        </a>

        <h1>Crear cuenta</h1>

<!-- formulario principal -->
        <form class="formulario-acceso formulario-acceso-registro" action="../app/controllers/procesar_registro.php" method="post">
            <input type="text" name="nombre" placeholder="Nombre" required autocomplete="given-name">
            <input type="text" name="apellidos" placeholder="Apellidos" required autocomplete="family-name">
            <input type="email" name="email" placeholder="Email" required autocomplete="email">
            <input type="password" name="password" placeholder="Contraseña" required autocomplete="new-password">
            <input type="text" name="telefono" placeholder="Telefono" required autocomplete="tel">
            <select name="sexo" required>
                <option value="">Sexo</option>
                <option value="hombre">Hombre</option>
                <option value="mujer">Mujer</option>
                <option value="otro">Otro</option>
            </select>
            <input type="date" name="fecha_nacimiento" required>
            <input type="text" name="ciudad" placeholder="Ciudad" required autocomplete="address-level2">

            <button type="submit">Registrarse</button>
        </form>

        <?php if (isset($_GET["error"]) && $_GET["error"] === "campos"): ?>
            <p style="color:red">Completa todos los campos correctamente.</p>
        <?php elseif (isset($_GET["error"]) && $_GET["error"] === "password"): ?>
            <p style="color:red">La contraseña debe tener al menos 4 caracteres.</p>
        <?php elseif (isset($_GET["error"]) && $_GET["error"] === "email"): ?>
            <p style="color:red">El email no tiene un formato válido.</p>
        <?php elseif (isset($_GET["error"]) && $_GET["error"] === "existe"): ?>
            <p style="color:red">Ese correo ya está registrado.</p>
        <?php elseif (isset($_GET["error"])): ?>
            <p style="color:red">No se ha podido completar el registro.</p>
        <?php endif; ?>

        <p class="enlace-acceso">
            ¿Ya tienes cuenta?
            <a href="login.php">Inicia sesión</a>
        </p>
    </div>

</body>
</html>

