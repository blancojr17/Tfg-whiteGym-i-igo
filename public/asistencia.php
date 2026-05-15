<?php
// registro y consulta de asistencia
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
$total_asistencias = 0;
$ultima_asistencia = null;
// mensajes segun el resultado
$mensaje_exito = "";
// mensajes segun el resultado
$mensaje_error = "";

// preparacion de la consulta
$stmtAsistencias = $conexion->prepare("SELECT COUNT(*) AS total, MAX(fecha) AS ultima_fecha FROM asistencia WHERE id_usuario = ?");
// comprobacion de la consulta
if ($stmtAsistencias) {
    $stmtAsistencias->bind_param("i", $id_usuario);
// ejecucion de la consulta
    $stmtAsistencias->execute();
    $resultado = $stmtAsistencias->get_result();
// lectura de resultados
    $fila = $resultado ? $resultado->fetch_assoc() : null;
    $stmtAsistencias->close();

    if ($fila) {
        $total_asistencias = (int) ($fila["total"] ?? 0);
        $ultima_asistencia = $fila["ultima_fecha"] ?? null;
    }
}

// recogida de parametros de la url
if (isset($_GET["asistencia_ok"]) && $_GET["asistencia_ok"] === "1") {
// mensajes segun el resultado
    $mensaje_exito = "Asistencia registrada correctamente.";
}

// recogida de parametros de la url
if (isset($_GET["asistencia_error"])) {
// recogida de parametros de la url
    $tipo_error = $_GET["asistencia_error"];

    if ($tipo_error === "sin_plan") {
// mensajes segun el resultado
        $mensaje_error = "No tienes ningun plan activo para registrar entrada.";
    } elseif ($tipo_error === "duplicada") {
// mensajes segun el resultado
        $mensaje_error = "Ya has registrado tu asistencia hoy.";
    } elseif ($tipo_error === "bono") {
// mensajes segun el resultado
        $mensaje_error = "No tienes usos disponibles en tu bono actual.";
    } else {
// mensajes segun el resultado
        $mensaje_error = "No se pudo registrar la asistencia. Intentalo de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia - WhiteGym</title>
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
                    <span class="eyebrow">Acceso gimnasio</span>
                    <h2>Asistencia</h2>
                    <p>Registra tu entrada y consulta de forma simple el seguimiento basico de accesos.</p>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

<!-- bloque de estadisticas -->
            <section class="stats-grid">
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Entradas</span>
                    <strong class="metric-value"><?php echo $total_asistencias; ?></strong>
                    <span class="metric-caption">Total acumulado de registros de asistencia.</span>
                </article>

<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Ultima visita</span>
                    <strong class="metric-value"><?php echo htmlspecialchars($ultima_asistencia ?: "-"); ?></strong>
                    <span class="metric-caption">Fecha del ultimo acceso que consta en el sistema.</span>
                </article>
            </section>

<!-- bloque principal de contenido -->
            <section class="card">
<!-- cabecera del bloque -->
                <div class="panel-header">
                    <div>
                        <h3>Registrar entrada</h3>
                        <p>Si tienes un plan valido, podras registrar una entrada por dia.</p>
                    </div>
                </div>

<!-- formulario principal -->
                <form action="../app/controllers/registrar_asistencia.php" method="POST" class="inline-actions">
                    <input type="hidden" name="origen" value="asistencia.php">
                    <button type="submit" class="btn btn-primary">Registrar entrada</button>
                    <a href="planes.php" class="btn btn-secondary">Ver mi plan</a>
                </form>
            </section>
        </div>
    </main>
</div>

</body>
</html>

