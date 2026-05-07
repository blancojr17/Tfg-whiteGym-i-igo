<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$plan_activo = null;
$reservas_totales = 0;
$proximas_clases = [];

$sql = "SELECT up.id_usuario_plan, up.id_plan, p.nombre, p.tipo, up.fecha_fin, up.usos_restantes
        FROM usuarios_planes up
        INNER JOIN planes p ON up.id_plan = p.id_plan
        WHERE up.id_usuario = ?
          AND up.fecha_fin >= CURDATE()
          AND (
                p.tipo = 'suscripcion'
                OR (p.tipo = 'bono' AND up.usos_restantes > 0)
              )
        ORDER BY up.fecha_inicio DESC, up.id_usuario_plan DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $plan_activo = $resultado ? $resultado->fetch_assoc() : null;
    $stmt->close();
}

$sqlReservas = "SELECT COUNT(*) AS total FROM usuarios_clases WHERE id_usuario = ?";
$stmtReservas = $conexion->prepare($sqlReservas);
if ($stmtReservas) {
    $stmtReservas->bind_param("i", $id_usuario);
    $stmtReservas->execute();
    $resReservas = $stmtReservas->get_result();
    $filaReservas = $resReservas ? $resReservas->fetch_assoc() : null;
    $reservas_totales = (int) ($filaReservas["total"] ?? 0);
    $stmtReservas->close();
}

$sqlProximas = "SELECT c.nombre, c.fecha
                FROM usuarios_clases uc
                INNER JOIN clases c ON uc.id_clase = c.id_clase
                WHERE uc.id_usuario = ? AND c.fecha >= NOW()
                ORDER BY c.fecha ASC
                LIMIT 4";
$stmtProximas = $conexion->prepare($sqlProximas);
if ($stmtProximas) {
    $stmtProximas->bind_param("i", $id_usuario);
    $stmtProximas->execute();
    $resProximas = $stmtProximas->get_result();
    while ($fila = $resProximas->fetch_assoc()) {
        $proximas_clases[] = $fila;
    }
    $stmtProximas->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente - WhiteGym</title>
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
                    <span class="eyebrow">Panel cliente</span>
                    <h2>Tu resumen en WhiteGym</h2>
                    <p>Consulta de forma rapida tu plan, tus reservas y los proximos pasos sin salir del dashboard principal.</p>
                </div>
                <div class="page-actions">
                    <a href="planes.php" class="btn btn-secondary">Ver planes</a>
                    <a href="clases.php" class="btn btn-primary">Reservar clase</a>
                </div>
            </div>

            <section class="stats-grid">
                <article class="card card-kpi">
                    <span class="eyebrow">Plan actual</span>
                    <strong class="metric-value"><?php echo htmlspecialchars($plan_activo["nombre"] ?? "Sin plan"); ?></strong>
                    <span class="metric-caption"><?php echo $plan_activo ? "Tu plan activo en este momento." : "Aun no tienes un plan contratado."; ?></span>
                </article>

                <article class="card card-kpi">
                    <span class="eyebrow">Modalidad</span>
                    <strong class="metric-value"><?php echo htmlspecialchars($plan_activo["tipo"] ?? "Pendiente"); ?></strong>
                    <span class="metric-caption">Suscripcion o bono segun tu contratacion.</span>
                </article>

                <article class="card card-kpi">
                    <span class="eyebrow">Reservas</span>
                    <strong class="metric-value"><?php echo $reservas_totales; ?></strong>
                    <span class="metric-caption">Clases reservadas desde tu cuenta.</span>
                </article>

                <article class="card card-kpi">
                    <span class="eyebrow">Usos restantes</span>
                    <strong class="metric-value"><?php echo isset($plan_activo["usos_restantes"]) ? (int) $plan_activo["usos_restantes"] : "-"; ?></strong>
                    <span class="metric-caption">Solo aplica si tu plan es un bono.</span>
                </article>
            </section>

            <section class="split-grid">
                <article class="card">
                    <div class="panel-header">
                        <div>
                            <h3>Estado de tu plan</h3>
                            <p>Resumen rapido de la suscripcion o bono que tienes activo.</p>
                        </div>
                        <?php if ($plan_activo): ?>
                            <span class="status-pill status-ok">Activo</span>
                        <?php else: ?>
                            <span class="status-pill status-muted">Sin plan</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($plan_activo): ?>
                        <div class="stack">
                            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($plan_activo["nombre"]); ?></p>
                            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($plan_activo["tipo"]); ?></p>
                            <p><strong>Fecha fin:</strong> <?php echo htmlspecialchars($plan_activo["fecha_fin"]); ?></p>
                            <?php if (isset($plan_activo["usos_restantes"])): ?>
                                <p><strong>Usos restantes:</strong> <?php echo (int) $plan_activo["usos_restantes"]; ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            No tienes ningun plan activo ahora mismo. Puedes contratar uno desde la seccion de planes.
                        </div>
                    <?php endif; ?>
                </article>

                <article class="card">
                    <div class="panel-header">
                        <div>
                            <h3>Accesos rapidos</h3>
                            <p>Movimientos frecuentes dentro del portal.</p>
                        </div>
                    </div>
                    <div class="quick-links">
                        <a href="mi_cuerpo.php" class="btn btn-secondary">Actualizar medidas</a>
                        <a href="mis_clases.php" class="btn btn-secondary">Ver mis clases</a>
                        <a href="asistencia.php" class="btn btn-secondary">Registrar asistencia</a>
                    </div>
                </article>
            </section>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Proximas clases</h3>
                        <p>Tus siguientes reservas confirmadas.</p>
                    </div>
                    <a href="mis_clases.php" class="btn btn-secondary">Ver todas</a>
                </div>

                <?php if (empty($proximas_clases)): ?>
                    <div class="empty-state">Aun no tienes clases proximas reservadas.</div>
                <?php else: ?>
                    <ul class="list-simple">
                        <?php foreach ($proximas_clases as $clase): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></strong>
                                <span class="muted"><?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>

</body>
</html>
