<?php
session_start();

if (!isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<header id="cabecera-admin">
    <h1>Panel de Administración</h1>
    <div id="usuario-admin">
        <span><?php echo $_SESSION["email"]; ?></span>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-admin">

    <section id="seccion-usuarios">
        <h2>Usuarios</h2>

        <table id="tabla-usuarios">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lenin Chiluisa</td>
                    <td>lenin@gmail.com</td>
                    <td>Premium</td>
                    <td class="estado-activo">Activo</td>
                    <td>
                        <button class="btn-editar">Editar</button>
                        <button class="btn-eliminar">Eliminar</button>
                    </td>
                </tr>
                <tr>
                    <td>Maite</td>
                    <td>maite@gmail.com</td>
                    <td>Básico</td>
                    <td class="estado-inactivo">Inactivo</td>
                    <td>
                        <button class="btn-editar">Editar</button>
                        <button class="btn-eliminar">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <section id="seccion-clases">
        <h2>Clases</h2>

        <div id="grid-clases">
            <div class="tarjeta-clase">
                <h3>Yoga</h3>
                <p>Con Manuel Castro</p>
                <p>Miércoles · 08:00 · Sala A</p>
                <span class="nivel-clase">Todos los niveles</span>
                <div class="acciones-clase">
                    <button class="btn-editar">Editar</button>
                    <button class="btn-eliminar">Eliminar</button>
                </div>
            </div>

            <div class="tarjeta-clase">
                <h3>CrossFit</h3>
                <p>Con Adam El Bakkali</p>
                <p>Miércoles · 10:00 · Sala B</p>
                <span class="nivel-clase">Avanzado</span>
                <div class="acciones-clase">
                    <button class="btn-editar">Editar</button>
                    <button class="btn-eliminar">Eliminar</button>
                </div>
            </div>

            <div class="tarjeta-clase">
                <h3>Spinning</h3>
                <p>Con Raduan Azzi</p>
                <p>Miércoles · 18:00 · Sala B</p>
                <span class="nivel-clase">Todos los niveles</span>
                <div class="acciones-clase">
                    <button class="btn-editar">Editar</button>
                    <button class="btn-eliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </section>

</main>

</body>
</html>
