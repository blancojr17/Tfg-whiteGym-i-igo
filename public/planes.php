<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$planes = [];
$error_planes = false;
$plan_actual = null;

$sqlPlanActual = "SELECT up.id_usuario_plan, up.id_plan, up.fecha_fin, up.usos_restantes, p.nombre, p.tipo, p.precio
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
$stmtPlanActual = $conexion->prepare($sqlPlanActual);
if ($stmtPlanActual) {
    $stmtPlanActual->bind_param("i", $id_usuario);
    $stmtPlanActual->execute();
    $resultadoPlanActual = $stmtPlanActual->get_result();
    $plan_actual = $resultadoPlanActual ? $resultadoPlanActual->fetch_assoc() : null;
    $stmtPlanActual->close();
}

$sql = "SELECT * FROM planes WHERE activo = 1 ORDER BY tipo ASC, precio ASC";
$resultado = $conexion->query($sql);

if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $planes[] = $fila;
    }
} else {
    $error_planes = true;
}

function beneficios_plan(array $plan): array
{
    $tipo = $plan["tipo"] ?? "";
    $duracion = (int) ($plan["duracion_dias"] ?? 0);
    $usos = (int) ($plan["usos"] ?? 0);

    if ($tipo === "suscripcion") {
        return [
            "Acceso durante " . $duracion . " dias",
            "Entrada diaria al gimnasio",
            "Reserva de clases desde tu panel"
        ];
    }

    return [
        $usos . " accesos disponibles",
        "Validez de " . $duracion . " dias",
        "Ideal para uso puntual del gimnasio"
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi plan - WhiteGym</title>
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
                    <span class="eyebrow">Planes activos</span>
                    <h2>Mi plan</h2>
                    <p>Elige un bono o una suscripcion. Solo puedes tener un plan activo a la vez.</p>
                </div>
            </div>

            <?php if (isset($_GET["ok"])): ?>
                <p class="notice-ok">Plan actualizado correctamente.</p>
            <?php endif; ?>

            <?php if (isset($_GET["error"])): ?>
                <p class="notice-error">No se ha podido completar la operacion.</p>
            <?php endif; ?>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Plan actual</h3>
                    </div>
                    <?php if ($plan_actual): ?>
                        <span class="status-pill status-ok">Activo</span>
                    <?php else: ?>
                        <span class="status-pill status-muted">Sin plan</span>
                    <?php endif; ?>
                </div>

                <?php if ($plan_actual): ?>
                    <div class="current-plan-summary">
                        <p><strong><?php echo htmlspecialchars($plan_actual["nombre"] ?? ""); ?></strong></p>
                        <p><?php echo htmlspecialchars($plan_actual["tipo"] ?? ""); ?></p>
                        <p>Fin: <?php echo htmlspecialchars($plan_actual["fecha_fin"] ?? ""); ?></p>
                        <?php if (($plan_actual["tipo"] ?? "") === "bono"): ?>
                            <p>Usos: <?php echo (int) ($plan_actual["usos_restantes"] ?? 0); ?></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">Todavia no tienes un plan activo.</div>
                <?php endif; ?>
            </section>

            <?php if ($error_planes): ?>
                <p class="notice-error">No se han podido cargar los planes.</p>
            <?php elseif (empty($planes)): ?>
                <div class="empty-state">No hay planes disponibles en este momento.</div>
            <?php else: ?>
                <section class="grid-cards plans-grid">
                    <?php foreach ($planes as $indice => $plan): ?>
                        <?php
                        $beneficios = beneficios_plan($plan);
                        $es_destacado = $indice === 1;
                        $es_actual = $plan_actual && (int) ($plan_actual["id_plan"] ?? 0) === (int) ($plan["id_plan"] ?? 0);
                        $mensaje_confirmacion = $plan_actual
                            ? "¿Seguro que quieres cambiar tu plan actual por " . addslashes((string) ($plan["nombre"] ?? "este plan")) . "?"
                            : "¿Seguro que quieres contratar " . addslashes((string) ($plan["nombre"] ?? "este plan")) . "?";
                        ?>
                        <article class="card plan-card<?php echo $es_destacado ? " featured" : ""; ?>">
                            <div class="plan-card-header">
                                <div>
                                    <h3 class="plan-card-name"><?php echo htmlspecialchars($plan["nombre"] ?? "Sin nombre"); ?></h3>
                                    <p class="plan-card-type"><?php echo htmlspecialchars($plan["tipo"] ?? ""); ?></p>
                                </div>
                                <?php if ($es_actual): ?>
                                    <span class="badge badge-accent">Actual</span>
                                <?php elseif ($es_destacado): ?>
                                    <span class="badge badge-accent">Destacado</span>
                                <?php endif; ?>
                            </div>

                            <p class="plan-price">
                                <?php echo htmlspecialchars((string) ($plan["precio"] ?? "0")); ?> EUR
                                <span><?php echo ($plan["tipo"] ?? "") === "suscripcion" ? "/plan" : "/bono"; ?></span>
                            </p>

                            <ul class="plan-benefits">
                                <?php foreach ($beneficios as $beneficio): ?>
                                    <li><?php echo htmlspecialchars($beneficio); ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <form action="../app/controllers/contratar_plan.php" method="post" class="plan-card-form" onsubmit="return confirm('<?php echo htmlspecialchars($mensaje_confirmacion, ENT_QUOTES, 'UTF-8'); ?>');">
                                <input type="hidden" name="id_plan" value="<?php echo (int) ($plan["id_plan"] ?? 0); ?>">
                                <button type="submit" class="btn btn-primary"><?php echo $plan_actual ? "Cambiar plan" : "Contratar"; ?></button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>
