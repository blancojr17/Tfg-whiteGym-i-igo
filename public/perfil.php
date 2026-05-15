<?php
// perfil del usuario
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$perfil = null;

// consulta sql
$sqlPerfil = "SELECT nombre, apellidos, email, telefono, sexo, fecha_nacimiento, ciudad, fecha_registro
              FROM usuarios
              WHERE id_usuario = ?
              LIMIT 1";
// preparacion de la consulta
$stmtPerfil = $conexion->prepare($sqlPerfil);
// comprobacion de la consulta
if ($stmtPerfil) {
    $stmtPerfil->bind_param("i", $id_usuario);
// ejecucion de la consulta
    $stmtPerfil->execute();
    $resultadoPerfil = $stmtPerfil->get_result();
// lectura de resultados
    $perfil = $resultadoPerfil ? $resultadoPerfil->fetch_assoc() : null;
    $stmtPerfil->close();
}

if (!$perfil) {
// redireccion final
    header("Location: cliente.php");
    exit;
}

// mensajes segun el resultado
$mensaje_exito = "";
// mensajes segun el resultado
$mensaje_error = "";

// recogida de parametros de la url
if (isset($_GET["ok"])) {
// recogida de parametros de la url
    if ($_GET["ok"] === "perfil") {
// mensajes segun el resultado
        $mensaje_exito = "Datos personales actualizados correctamente.";
// recogida de parametros de la url
    } elseif ($_GET["ok"] === "password") {
// mensajes segun el resultado
        $mensaje_exito = "Contrasena actualizada correctamente.";
    }
}

// recogida de parametros de la url
if (isset($_GET["error"])) {
// recogida de parametros de la url
    if ($_GET["error"] === "email") {
// mensajes segun el resultado
        $mensaje_error = "Ese email ya esta siendo usado por otra cuenta.";
// recogida de parametros de la url
    } elseif ($_GET["error"] === "actual") {
// mensajes segun el resultado
        $mensaje_error = "La contrasena actual no es correcta.";
    } else {
// mensajes segun el resultado
        $mensaje_error = "No se pudo completar la operacion.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/cliente.css">
</head>
<body>

<?php include __DIR__ . "/includes/topbar.php"; ?>

<!-- estructura principal del panel -->
<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_cliente.php"; ?>

<!-- contenido principal -->
    <main class="dashboard-main">
        <div class="page-shell">
<!-- cabecera del contenido -->
            <div class="page-header">
                <div>
                    <span class="eyebrow">Perfil</span>
                    <h2>Mis datos</h2>
                    <p>Actualiza tu informacion personal y tu contrasena sin salir del area cliente.</p>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

<!-- bloques de resumen -->
            <section class="split-grid profile-grid">
<!-- tarjeta de contenido -->
                <article class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Datos personales</h3>
                            <p>Tu informacion basica de acceso y contacto.</p>
                        </div>
                    </div>

<!-- formulario principal -->
                    <form action="../app/controllers/actualizar_perfil.php" method="POST" class="form-grid two-columns">
                        <div class="field">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($perfil["nombre"] ?? ""); ?>" required>
                        </div>
                        <div class="field">
                            <label for="apellidos">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($perfil["apellidos"] ?? ""); ?>" required>
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($perfil["email"] ?? ""); ?>" required>
                        </div>
                        <div class="field">
                            <label for="telefono">Telefono</label>
                            <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($perfil["telefono"] ?? ""); ?>">
                        </div>
                        <div class="field">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo">
                                <option value="">Selecciona una opcion</option>
                                <option value="hombre" <?php echo ($perfil["sexo"] ?? "") === "hombre" ? "selected" : ""; ?>>Hombre</option>
                                <option value="mujer" <?php echo ($perfil["sexo"] ?? "") === "mujer" ? "selected" : ""; ?>>Mujer</option>
                                <option value="otro" <?php echo ($perfil["sexo"] ?? "") === "otro" ? "selected" : ""; ?>>Otro</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="fecha_nacimiento">Fecha nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($perfil["fecha_nacimiento"] ?? ""); ?>">
                        </div>
                        <div class="field">
                            <label for="ciudad">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($perfil["ciudad"] ?? ""); ?>">
                        </div>
                        <div class="field">
                            <label for="fecha_registro">Fecha registro</label>
                            <input type="text" id="fecha_registro" value="<?php echo htmlspecialchars($perfil["fecha_registro"] ?? ""); ?>" disabled>
                        </div>
                        <div class="inline-actions profile-actions">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </article>

<!-- tarjeta de contenido -->
                <article class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Seguridad</h3>
                            <p>Cambia tu contrasena manteniendo el acceso a tu cuenta.</p>
                        </div>
                    </div>

<!-- formulario principal -->
                    <form action="../app/controllers/actualizar_password.php" method="POST" class="stack">
                        <div class="field">
                            <label for="password_actual">Contrasena actual</label>
                            <input type="password" id="password_actual" name="password_actual" required>
                        </div>
                        <div class="field">
                            <label for="password_nueva">Nueva contrasena</label>
                            <input type="password" id="password_nueva" name="password_nueva" required>
                        </div>
                        <div class="field">
                            <label for="password_repetida">Repetir contrasena</label>
                            <input type="password" id="password_repetida" name="password_repetida" required>
                        </div>
                        <div class="inline-actions profile-actions">
                            <button type="submit" class="btn btn-primary">Actualizar contrasena</button>
                        </div>
                    </form>
                </article>
            </section>
        </div>
    </main>
</div>

</body>
</html>

