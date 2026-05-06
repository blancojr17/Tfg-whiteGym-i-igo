<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$id_admin_actual = (int) $_SESSION["id_usuario"];

$total_usuarios = 0;
$total_entrenadores = 0;
$total_clases = 0;
$total_reservas = 0;
$usuarios = [];
$error_datos = false;

$sqlTotales = "SELECT
                (SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario') AS total_usuarios,
                (SELECT COUNT(*) FROM usuarios WHERE rol = 'entrenador') AS total_entrenadores,
                (SELECT COUNT(*) FROM clases) AS total_clases,
                (SELECT COUNT(*) FROM usuarios_clases) AS total_reservas";
$stmtTotales = $conexion->prepare($sqlTotales);

if ($stmtTotales) {
    $stmtTotales->execute();
    $resultadoTotales = $stmtTotales->get_result();
    $filaTotales = $resultadoTotales ? $resultadoTotales->fetch_assoc() : null;
    $stmtTotales->close();

    if ($filaTotales) {
        $total_usuarios = (int) ($filaTotales["total_usuarios"] ?? 0);
        $total_entrenadores = (int) ($filaTotales["total_entrenadores"] ?? 0);
        $total_clases = (int) ($filaTotales["total_clases"] ?? 0);
        $total_reservas = (int) ($filaTotales["total_reservas"] ?? 0);
    }
} else {
    $error_datos = true;
}

$sqlUsuarios = "SELECT id_usuario, nombre, email, rol, activo
                FROM usuarios
                ORDER BY id_usuario ASC";
$stmtUsuarios = $conexion->prepare($sqlUsuarios);

if ($stmtUsuarios) {
    $stmtUsuarios->execute();
    $resultadoUsuarios = $stmtUsuarios->get_result();

    while ($fila = $resultadoUsuarios->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    $stmtUsuarios->close();
} else {
    $error_datos = true;
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET["ok"]) && $_GET["ok"] === "actualizado") {
    $mensaje_exito = "Usuario actualizado correctamente.";
}

if (isset($_GET["error"])) {
    $tipo_error = $_GET["error"];

    if ($tipo_error === "id") {
        $mensaje_error = "El usuario no es válido.";
    } elseif ($tipo_error === "rol") {
        $mensaje_error = "El rol no es válido.";
    } elseif ($tipo_error === "activo") {
        $mensaje_error = "El estado activo no es válido.";
    } elseif ($tipo_error === "autoproteccion") {
        $mensaje_error = "No puedes desactivarte ni quitarte el rol de admin.";
    } elseif ($tipo_error === "no_existe") {
        $mensaje_error = "El usuario no existe.";
    } else {
        $mensaje_error = "No se pudo completar la operación.";
    }
}

// Base preparada para futuras ampliaciones del panel admin.
$admin_modulos_futuros = [
    "gestion_clases_completa" => false,
    "gestion_planes_completa" => false,
    "dashboard_avanzado" => false,
    "graficos" => false,
    "estadisticas" => false,
    "logs" => false
];
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
        <span><?php echo htmlspecialchars($_SESSION["email"]); ?></span>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-admin">

    <?php if ($mensaje_exito !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>

    <?php if ($mensaje_error !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje_error); ?></p>
    <?php endif; ?>

    <?php if ($error_datos): ?>
        <p>No se pudieron cargar todos los datos del panel.</p>
    <?php endif; ?>

    <section id="seccion-resumen">
        <h2>Resumen</h2>
        <p><strong>Total usuarios:</strong> <?php echo $total_usuarios; ?></p>
        <p><strong>Total entrenadores:</strong> <?php echo $total_entrenadores; ?></p>
        <p><strong>Total clases:</strong> <?php echo $total_clases; ?></p>
        <p><strong>Total reservas:</strong> <?php echo $total_reservas; ?></p>
    </section>

    <section id="seccion-usuarios">
        <h2>Usuarios</h2>

        <?php if (empty($usuarios)): ?>
            <p>No hay usuarios registrados.</p>
        <?php else: ?>
            <table id="tabla-usuarios">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Activo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario["nombre"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($usuario["email"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($usuario["rol"] ?? ""); ?></td>
                            <td><?php echo ((int) ($usuario["activo"] ?? 0)) === 1 ? "Sí" : "No"; ?></td>
                            <td>
                                <form action="../app/controllers/gestionar_usuario.php" method="POST">
                                    <input type="hidden" name="id_usuario" value="<?php echo (int) ($usuario["id_usuario"] ?? 0); ?>">

                                    <label>
                                        Rol
                                        <select name="rol">
                                            <option value="usuario" <?php echo ($usuario["rol"] === "usuario") ? "selected" : ""; ?>>usuario</option>
                                            <option value="entrenador" <?php echo ($usuario["rol"] === "entrenador") ? "selected" : ""; ?>>entrenador</option>
                                            <option value="admin" <?php echo ($usuario["rol"] === "admin") ? "selected" : ""; ?>>admin</option>
                                        </select>
                                    </label>

                                    <label>
                                        Activo
                                        <select name="activo">
                                            <option value="1" <?php echo ((int) $usuario["activo"] === 1) ? "selected" : ""; ?>>Sí</option>
                                            <option value="0" <?php echo ((int) $usuario["activo"] === 0) ? "selected" : ""; ?>>No</option>
                                        </select>
                                    </label>

                                    <button type="submit">Actualizar</button>
                                </form>

                                <?php if ((int) $usuario["id_usuario"] === $id_admin_actual): ?>
                                    <small>Tu usuario admin actual</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
