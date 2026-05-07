<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$total_asistencias = 0;
$ultima_asistencia = null;
$mensaje_exito = "";
$mensaje_error = "";

$stmtAsistencias = $conexion->prepare("SELECT COUNT(*) AS total, MAX(fecha) AS ultima_fecha FROM asistencia WHERE id_usuario = ?");
if ($stmtAsistencias) {
    $stmtAsistencias->bind_param("i", $id_usuario);
    $stmtAsistencias->execute();
    $resultado = $stmtAsistencias->get_result();
    $fila = $resultado ? $resultado->fetch_assoc() : null;
    $stmtAsistencias->close();

    if ($fila) {
        $total_asistencias = (int) ($fila["total"] ?? 0);
        $ultima_asistencia = $fila["ultima_fecha"] ?? null;
    }
}

if (isset($_GET["asistencia_ok"]) && $_GET["asistencia_ok"] === "1") {
    $mensaje_exito = "Asistencia registrada correctamente.";
}

if (isset($_GET["asistencia_error"])) {
    $tipo_error = $_GET["asistencia_error"];

    if ($tipo_error === "sin_plan") {
        $mensaje_error = "No tienes ningun plan activo para registrar entrada.";
    } elseif ($tipo_error === "duplicada") {
        $mensaje_error = "Ya has registrado tu asistencia hoy.";
    } elseif ($tipo_error === "bono") {
        $mensaje_error = "No tienes usos disponibles en tu bono actual.";
    } else {
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

<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_cliente.php"; ?>

    <main class="dashboard-main">
        <div class="page-shell">
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

            <section class="stats-grid">
                <article class="card card-kpi">
                    <span class="eyebrow">Entradas</span>
                    <strong class="metric-value"><?php echo $total_asistencias; ?></strong>
                    <span class="metric-caption">Total acumulado de registros de asistencia.</span>
                </article>

                <article class="card card-kpi">
                    <span class="eyebrow">Ultima visita</span>
                    <strong class="metric-value"><?php echo htmlspecialchars($ultima_asistencia ?: "-"); ?></strong>
                    <span class="metric-caption">Fecha del ultimo acceso que consta en el sistema.</span>
                </article>
            </section>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Registrar entrada</h3>
                        <p>Si tienes un plan valido, podras registrar una entrada por dia.</p>
                    </div>
                </div>

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
