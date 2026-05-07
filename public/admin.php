<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$total_usuarios = 0;
$total_entrenadores = 0;
$total_clases = 0;
$total_reservas = 0;
$clases_mas_llenas = [];
$ultimas_clases = [];
$ultimos_usuarios = [];
$error_datos = false;

$stmtTotales = $conexion->prepare("SELECT
    (SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario') AS total_usuarios,
    (SELECT COUNT(*) FROM usuarios WHERE rol = 'entrenador') AS total_entrenadores,
    (SELECT COUNT(*) FROM clases) AS total_clases,
    (SELECT COUNT(*) FROM usuarios_clases) AS total_reservas");
if ($stmtTotales) {
    $stmtTotales->execute();
    $resTotales = $stmtTotales->get_result();
    $fila = $resTotales ? $resTotales->fetch_assoc() : null;
    $stmtTotales->close();
    if ($fila) {
        $total_usuarios = (int) ($fila["total_usuarios"] ?? 0);
        $total_entrenadores = (int) ($fila["total_entrenadores"] ?? 0);
        $total_clases = (int) ($fila["total_clases"] ?? 0);
        $total_reservas = (int) ($fila["total_reservas"] ?? 0);
    }
} else {
    $error_datos = true;
}

$stmtTop = $conexion->prepare("SELECT c.nombre, c.capacidad, COUNT(uc.id_usuario_clase) AS ocupadas
    FROM clases c
    LEFT JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
    GROUP BY c.id_clase, c.nombre, c.capacidad
    ORDER BY ocupadas DESC, c.capacidad DESC
    LIMIT 5");
if ($stmtTop) {
    $stmtTop->execute();
    $resTop = $stmtTop->get_result();
    while ($f = $resTop->fetch_assoc()) {
        $clases_mas_llenas[] = $f;
    }
    $stmtTop->close();
} else {
    $error_datos = true;
}

$stmtUltimasClases = $conexion->prepare("SELECT c.nombre, c.fecha, c.capacidad,
    CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidos, '')) AS entrenador
    FROM clases c
    LEFT JOIN usuarios u ON c.id_entrenador = u.id_usuario
    ORDER BY c.id_clase DESC
    LIMIT 5");
if ($stmtUltimasClases) {
    $stmtUltimasClases->execute();
    $resUltimasClases = $stmtUltimasClases->get_result();
    while ($fila = $resUltimasClases->fetch_assoc()) {
        $ultimas_clases[] = $fila;
    }
    $stmtUltimasClases->close();
} else {
    $error_datos = true;
}

$stmtUltimos = $conexion->prepare("SELECT nombre, email, rol, activo FROM usuarios ORDER BY id_usuario DESC LIMIT 5");
if ($stmtUltimos) {
    $stmtUltimos->execute();
    $resUltimos = $stmtUltimos->get_result();
    while ($u = $resUltimos->fetch_assoc()) {
        $ultimos_usuarios[] = $u;
    }
    $stmtUltimos->close();
} else {
    $error_datos = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admin - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>

<?php include __DIR__ . "/includes/topbar.php"; ?>

<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_admin.php"; ?>

    <main class="dashboard-main">
        <div class="page-shell">
            <div class="page-header">
                <div>
                    <h2>Dashboard</h2>
                </div>
            </div>

            <?php if ($error_datos): ?>
                <p class="notice-error">No se pudieron cargar algunos datos del dashboard.</p>
            <?php endif; ?>

            <section class="stats-grid">
                <article class="card card-kpi">
                    <span class="eyebrow">Usuarios</span>
                    <strong class="metric-value"><?php echo $total_usuarios; ?></strong>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Entrenadores</span>
                    <strong class="metric-value"><?php echo $total_entrenadores; ?></strong>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Clases</span>
                    <strong class="metric-value"><?php echo $total_clases; ?></strong>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Reservas</span>
                    <strong class="metric-value"><?php echo $total_reservas; ?></strong>
                </article>
            </section>

            <section class="split-grid">
                <article class="card">
                    <div class="panel-header">
                        <div>
                            <h3>Ultimas clases</h3>
                        </div>
                        <a href="admin_clases.php" class="btn btn-secondary">Gestionar</a>
                    </div>

                    <?php if (empty($ultimas_clases)): ?>
                        <div class="empty-state">Todavia no hay clases registradas.</div>
                    <?php else: ?>
                        <ul class="list-simple">
                            <?php foreach ($ultimas_clases as $clase): ?>
                                <?php $entrenador = trim((string) ($clase["entrenador"] ?? "")); ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></strong>
                                    <span class="muted"><?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></span><br>
                                    <span class="subtle">Entrenador: <?php echo htmlspecialchars($entrenador !== "" ? $entrenador : "Sin asignar"); ?> | Capacidad: <?php echo (int) ($clase["capacidad"] ?? 0); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>

                <article class="card">
                    <div class="panel-header">
                        <div>
                            <h3>Clases mas llenas</h3>
                        </div>
                    </div>

                    <?php if (empty($clases_mas_llenas)): ?>
                        <div class="empty-state">No hay datos de reservas todavia.</div>
                    <?php else: ?>
                        <ul class="list-simple">
                            <?php foreach ($clases_mas_llenas as $clase): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></strong>
                                    <span class="subtle"><?php echo (int) ($clase["ocupadas"] ?? 0); ?> / <?php echo (int) ($clase["capacidad"] ?? 0); ?> plazas ocupadas</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </article>
            </section>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Ultimos usuarios</h3>
                    </div>
                    <a href="admin_usuarios.php" class="btn btn-secondary">Ver usuarios</a>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos_usuarios as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u["nombre"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($u["email"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($u["rol"] ?? ""); ?></td>
                                    <td><?php echo ((int) ($u["activo"] ?? 0)) === 1 ? "Si" : "No"; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</div>

</body>
</html>
