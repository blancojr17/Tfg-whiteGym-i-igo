<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
     <link rel="stylesheet" href="../acceso.css"/>
</head>
<body class="pagina-acceso">

    <div class="contenedor-acceso">
        <a href="../index.html" class="logo-acceso">
            <img src="../assets/img/logosin.png" alt="Logo WhiteGym">
        </a>
        <h1>Crear cuenta</h1>

        <form class="formulario-acceso" action="procesar_registro.php" method="post">

            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="text" name="apellidos" placeholder="Apellidos" required>

            <input type="email" name="email" placeholder="Email" required>
            <input type="tel" name="telefono" placeholder="Teléfono">

            <select name="sexo" required>
                <option value="">Sexo</option>
                <option value="hombre">Hombre</option>
                <option value="mujer">Mujer</option>
                
            </select>

            <input type="password" name="password" placeholder="Contraseña" required>

            <button type="submit">Registrarse</button>
        </form>
        <?php if (isset($_GET["error"])): ?>
        <p style="color:red">Ese correo ya está registrado</p>
    <?php endif; ?>

        <p class="enlace-acceso">
            ¿Ya tienes cuenta?
            <a href="login.php">Inicia sesión</a>
        </p>
    </div>

</body>
</html>