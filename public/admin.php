<?php
// panel principal de administracion
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
// redireccion final
    header("Location: login.php");
    exit;
}

$total_usuarios = 0;
$total_entrenadores = 0;
$total_clases = 0;
$total_reservas = 0;
$ingresos_estimados = 0.0;
$clases_mas_llenas = [];
$distribucion_planes = [];
$clase_mas_popular = null;
$ultimas_clases = [];
$ultimos_usuarios = [];
// mensajes segun el resultado
$error_datos = false;

// preparacion de la consulta
$stmtTotales = $conexion->prepare("SELECT
    (SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario') AS total_usuarios,
    (SELECT COUNT(*) FROM usuarios WHERE rol = 'entrenador') AS total_entrenadores,
    (SELECT COUNT(*) FROM clases) AS total_clases,
    (SELECT COUNT(*) FROM usuarios_clases) AS total_reservas");
// comprobacion de la consulta
if ($stmtTotales) {
// ejecucion de la consulta
    $stmtTotales->execute();
    $resTotales = $stmtTotales->get_result();
// lectura de resultados
    $fila = $resTotales ? $resTotales->fetch_assoc() : null;
    $stmtTotales->close();
    if ($fila) {
        $total_usuarios = (int) ($fila["total_usuarios"] ?? 0);
        $total_entrenadores = (int) ($fila["total_entrenadores"] ?? 0);
        $total_clases = (int) ($fila["total_clases"] ?? 0);
        $total_reservas = (int) ($fila["total_reservas"] ?? 0);
    }
} else {
// mensajes segun el resultado
    $error_datos = true;
}

// consulta sql
$sqlIngresos = "SELECT COALESCE(SUM(p.precio), 0) AS ingresos_estimados
    FROM usuarios_planes up
    INNER JOIN planes p ON up.id_plan = p.id_plan
    WHERE up.fecha_fin >= CURDATE()
      AND (
          p.tipo = 'suscripcion'
          OR (p.tipo = 'bono' AND up.usos_restantes > 0)
      )";
// preparacion de la consulta
$stmtIngresos = $conexion->prepare($sqlIngresos);
// comprobacion de la consulta
if ($stmtIngresos) {
// ejecucion de la consulta
    $stmtIngresos->execute();
    $resIngresos = $stmtIngresos->get_result();
// lectura de resultados
    $filaIngresos = $resIngresos ? $resIngresos->fetch_assoc() : null;
    $ingresos_estimados = (float) ($filaIngresos["ingresos_estimados"] ?? 0);
    $stmtIngresos->close();
} else {
// mensajes segun el resultado
    $error_datos = true;
}

// consulta sql
$sqlDistribucionPlanes = "SELECT p.nombre, COUNT(*) AS total_usuarios_plan
    FROM usuarios_planes up
    INNER JOIN planes p ON up.id_plan = p.id_plan
    WHERE up.fecha_fin >= CURDATE()
      AND (
          p.tipo = 'suscripcion'
          OR (p.tipo = 'bono' AND up.usos_restantes > 0)
      )
    GROUP BY p.id_plan, p.nombre
    ORDER BY total_usuarios_plan DESC, p.nombre ASC";
// preparacion de la consulta
$stmtDistribucionPlanes = $conexion->prepare($sqlDistribucionPlanes);
// comprobacion de la consulta
if ($stmtDistribucionPlanes) {
// ejecucion de la consulta
    $stmtDistribucionPlanes->execute();
    $resDistribucionPlanes = $stmtDistribucionPlanes->get_result();
    $total_planes_activos = 0;

// lectura de resultados
    while ($filaPlan = $resDistribucionPlanes->fetch_assoc()) {
        $distribucion_planes[] = $filaPlan;
        $total_planes_activos += (int) ($filaPlan["total_usuarios_plan"] ?? 0);
    }

    $stmtDistribucionPlanes->close();

    if ($total_planes_activos > 0) {
        foreach ($distribucion_planes as &$planDistribucion) {
            $usuarios_plan = (int) ($planDistribucion["total_usuarios_plan"] ?? 0);
            $planDistribucion["porcentaje"] = round(($usuarios_plan / $total_planes_activos) * 100, 1);
        }
        unset($planDistribucion);
    }
} else {
// mensajes segun el resultado
    $error_datos = true;
}

// consulta sql
$sqlClasePopular = "SELECT c.nombre, COUNT(uc.id_usuario_clase) AS total_reservas
    FROM clases c
    INNER JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
    GROUP BY c.nombre
    ORDER BY total_reservas DESC, c.nombre ASC
    LIMIT 1";
// preparacion de la consulta
$stmtClasePopular = $conexion->prepare($sqlClasePopular);
// comprobacion de la consulta
if ($stmtClasePopular) {
// ejecucion de la consulta
    $stmtClasePopular->execute();
    $resClasePopular = $stmtClasePopular->get_result();
// lectura de resultados
    $clase_mas_popular = $resClasePopular ? $resClasePopular->fetch_assoc() : null;
    $stmtClasePopular->close();
} else {
// mensajes segun el resultado
    $error_datos = true;
}

// preparacion de la consulta
$stmtTop = $conexion->prepare("SELECT c.nombre, c.capacidad, COUNT(uc.id_usuario_clase) AS ocupadas
    FROM clases c
    LEFT JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
    GROUP BY c.id_clase, c.nombre, c.capacidad
    ORDER BY ocupadas DESC, c.capacidad DESC
    LIMIT 5");
// comprobacion de la consulta
if ($stmtTop) {
// ejecucion de la consulta
    $stmtTop->execute();
    $resTop = $stmtTop->get_result();
// lectura de resultados
    while ($f = $resTop->fetch_assoc()) {
        $clases_mas_llenas[] = $f;
    }
    $stmtTop->close();
} else {
// mensajes segun el resultado
    $error_datos = true;
}

// preparacion de la consulta
$stmtUltimasClases = $conexion->prepare("SELECT c.nombre, c.fecha, c.capacidad,
    CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidos, '')) AS entrenador
    FROM clases c
    LEFT JOIN usuarios u ON c.id_entrenador = u.id_usuario
    ORDER BY c.id_clase DESC
    LIMIT 5");
// comprobacion de la consulta
if ($stmtUltimasClases) {
// ejecucion de la consulta
    $stmtUltimasClases->execute();
    $resUltimasClases = $stmtUltimasClases->get_result();
// lectura de resultados
    while ($fila = $resUltimasClases->fetch_assoc()) {
        $ultimas_clases[] = $fila;
    }
    $stmtUltimasClases->close();
} else {
// mensajes segun el resultado
    $error_datos = true;
}

// preparacion de la consulta
$stmtUltimos = $conexion->prepare("SELECT nombre, email, rol, activo FROM usuarios ORDER BY id_usuario DESC LIMIT 5");
// comprobacion de la consulta
if ($stmtUltimos) {
// ejecucion de la consulta
    $stmtUltimos->execute();
    $resUltimos = $stmtUltimos->get_result();
// lectura de resultados
    while ($u = $resUltimos->fetch_assoc()) {
        $ultimos_usuarios[] = $u;
    }
    $stmtUltimos->close();
} else {
// mensajes segun el resultado
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

<!-- estructura principal del panel -->
<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_admin.php"; ?>

<!-- contenido principal -->
    <main class="dashboard-main">
        <div class="page-shell">
<!-- cabecera del contenido -->
            <div class="page-header">
                <div>
                    <h2>Dashboard</h2>
                </div>
            </div>

            <?php if ($error_datos): ?>
                <p class="notice-error">No se pudieron cargar algunos datos del dashboard.</p>
            <?php endif; ?>

<!-- bloque de estadisticas -->
            <section class="stats-grid">
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Usuarios</span>
                    <strong class="metric-value"><?php echo $total_usuarios; ?></strong>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Entrenadores</span>
                    <strong class="metric-value"><?php echo $total_entrenadores; ?></strong>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Clases</span>
                    <strong class="metric-value"><?php echo $total_clases; ?></strong>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Reservas</span>
                    <strong class="metric-value"><?php echo $total_reservas; ?></strong>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Ingresos estimados</span>
                    <strong class="metric-value"><?php echo number_format($ingresos_estimados, 2, ",", "."); ?> EUR</strong>
                    
                </article>
            </section>

<!-- bloques de resumen -->
            <section class="split-grid">
<!-- tarjeta de contenido -->
                <article class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Distribucion de planes</h3>
                            
                        </div>
                    </div>

                    <?php if (empty($distribucion_planes)): ?>
                        <div class="empty-state">No hay planes activos para calcular la distribucion.</div>
                    <?php else: ?>
                        <div class="plan-distribution">
                            <?php foreach ($distribucion_planes as $plan): ?>
                                <?php $porcentaje = (float) ($plan["porcentaje"] ?? 0); ?>
                                <div class="plan-distribution-item">
                                    <div class="plan-distribution-label">
                                        <strong><?php echo htmlspecialchars($plan["nombre"] ?? ""); ?></strong>
                                        <span><?php echo number_format($porcentaje, 1, ",", "."); ?>%</span>
                                    </div>
                                    <progress
                                        class="plan-distribution-bar"
                                        max="100"
                                        value="<?php echo max(0, min(100, $porcentaje)); ?>"
                                    ><?php echo number_format($porcentaje, 1, ",", "."); ?>%</progress>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>

<!-- tarjeta de contenido -->
                <article class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Clase mas popular</h3>
                            <p>Clase con mas reservas acumuladas.</p>
                        </div>
                    </div>

                    <?php if ($clase_mas_popular): ?>
                        <span class="eyebrow">Mas reservada</span>
                        <strong class="metric-value"><?php echo htmlspecialchars($clase_mas_popular["nombre"] ?? ""); ?></strong>
                        <span class="metric-caption"><?php echo (int) ($clase_mas_popular["total_reservas"] ?? 0); ?> reservas</span>
                    <?php else: ?>
                        <div class="empty-state">Todavia no hay reservas registradas.</div>
                    <?php endif; ?>
                </article>
            </section>

<!-- bloques de resumen -->
            <section class="split-grid">
<!-- tarjeta de contenido -->
                <article class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Ultimas clases</h3>
                        </div>
                        <a href="admin_clases.php" class="btn btn-secondary">Gestionar</a>
                    </div>

                    <?php if (empty($ultimas_clases)): ?>
                        <div class="empty-state">Todavia no hay clases registradas.</div>
                    <?php else: ?>
<!-- listado rapido de datos -->
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

<!-- tarjeta de contenido -->
                <article class="card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <h3>Clases mas llenas</h3>
                        </div>
                    </div>

                    <?php if (empty($clases_mas_llenas)): ?>
                        <div class="empty-state">No hay datos de reservas todavia.</div>
                    <?php else: ?>
<!-- listado rapido de datos -->
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

<!-- bloque principal de contenido -->
            <section class="card">
<!-- cabecera del bloque -->
                <div class="panel-header">
                    <div>
                        <h3>Ultimos usuarios</h3>
                    </div>
                    <a href="admin_usuarios.php" class="btn btn-secondary">Ver usuarios</a>
                </div>

<!-- contenedor de la tabla -->
                <div class="table-wrap">
<!-- tabla de datos -->
                    <table>
<!-- cabecera de la tabla -->
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Activo</th>
                            </tr>
                        </thead>
<!-- contenido de la tabla -->
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

