<?php
// listado general de clases
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
// redireccion final
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$clases = [];
// mensajes segun el resultado
$error_clases = false;

// consulta sql
$sql = "SELECT c.id_clase,
               c.nombre,
               c.descripcion,
               c.fecha,
               c.capacidad,
               CONCAT(COALESCE(e.nombre, ''), ' ', COALESCE(e.apellidos, '')) AS entrenador,
               COUNT(uc.id_usuario_clase) AS plazas_ocupadas,
               MAX(CASE WHEN uc.id_usuario = ? THEN 1 ELSE 0 END) AS usuario_apuntado
        FROM clases c
        LEFT JOIN usuarios e ON c.id_entrenador = e.id_usuario
        LEFT JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
        GROUP BY c.id_clase, c.nombre, c.descripcion, c.fecha, c.capacidad, e.nombre, e.apellidos
        ORDER BY c.fecha ASC";

// preparacion de la consulta
$stmt = $conexion->prepare($sql);
// comprobacion de la consulta
if ($stmt) {
    $stmt->bind_param("i", $id_usuario);
// ejecucion de la consulta
    $stmt->execute();
    $resultado = $stmt->get_result();

// lectura de resultados
    while ($fila = $resultado->fetch_assoc()) {
        $clases[] = $fila;
    }

    $stmt->close();
} else {
// mensajes segun el resultado
    $error_clases = true;
}

// mensajes segun el resultado
$mensaje_exito = "";
// mensajes segun el resultado
$mensaje_error = "";

// recogida de parametros de la url
if (isset($_GET["ok"])) {
// recogida de parametros de la url
    if ($_GET["ok"] === "apuntado") {
// mensajes segun el resultado
        $mensaje_exito = "Te has apuntado correctamente a la clase.";
// recogida de parametros de la url
    } elseif ($_GET["ok"] === "desapuntado") {
// mensajes segun el resultado
        $mensaje_exito = "Te has desapuntado correctamente de la clase.";
    }
}

// recogida de parametros de la url
if (isset($_GET["error"])) {
// mensajes segun el resultado
    $mensaje_error = "No se pudo completar la operacion.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar clases - WhiteGym</title>
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
                    <span class="eyebrow">Clases</span>
                    <h2>Reservar clases</h2>
                    <p>Explora las clases disponibles y gestiona tu plaza con una experiencia mas clara y ordenada.</p>
                </div>
                <div class="page-actions">
                    <a href="mis_clases.php" class="btn btn-secondary">Ver mis clases</a>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <?php if ($error_clases): ?>
                <p class="notice-error">No se han podido cargar las clases.</p>
            <?php elseif (empty($clases)): ?>
                <div class="empty-state">No hay clases disponibles en este momento.</div>
            <?php else: ?>
<!-- bloque principal de contenido -->
                <section class="grid-cards">
                    <?php foreach ($clases as $clase): ?>
                        <?php
                        $id_clase = (int) ($clase["id_clase"] ?? 0);
                        $capacidad = (int) ($clase["capacidad"] ?? 0);
                        $ocupadas = (int) ($clase["plazas_ocupadas"] ?? 0);
                        $apuntado = ((int) ($clase["usuario_apuntado"] ?? 0)) === 1;
                        $llena = $capacidad > 0 && $ocupadas >= $capacidad;
                        $entrenador = trim((string) ($clase["entrenador"] ?? ""));
                        $ocupacion = $capacidad > 0 ? min(100, (int) round(($ocupadas / $capacidad) * 100)) : 0;
                        $plazas_restantes = max(0, $capacidad - $ocupadas);
// mensajes segun el resultado
                        $estado_ocupacion = "low";

                        if ($ocupacion > 80) {
// mensajes segun el resultado
                            $estado_ocupacion = "high";
                        } elseif ($ocupacion > 50) {
// mensajes segun el resultado
                            $estado_ocupacion = "medium";
                        }

                        if ($plazas_restantes === 0) {
                            $texto_plazas = "Clase completa";
                        } elseif ($plazas_restantes <= 2) {
                            $texto_plazas = "Clase casi completa";
                        } else {
                            $texto_plazas = "Quedan " . $plazas_restantes . " plazas";
                        }
                        ?>
<!-- tarjeta de contenido -->
                        <article class="card">
<!-- cabecera del bloque -->
                            <div class="panel-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($clase["nombre"] ?? "Sin nombre"); ?></h3>
                                    <p><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></p>
                                </div>
                                <?php if ($apuntado): ?>
                                    <span class="status-pill status-ok">Apuntado</span>
                                <?php elseif ($llena): ?>
                                    <span class="status-pill status-muted">Clase llena</span>
                                <?php else: ?>
                                    <span class="status-pill status-accent">Plazas libres</span>
                                <?php endif; ?>
                            </div>

                            <div class="class-card-meta">
                                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></p>
                                <p><strong>Entrenador:</strong> <?php echo htmlspecialchars($entrenador !== "" ? $entrenador : "Sin asignar"); ?></p>
                                <p><strong>Plazas:</strong> <?php echo $ocupadas; ?> / <?php echo $capacidad; ?></p>
                            </div>

                            <div class="class-occupancy">
                                <div class="class-occupancy-head">
                                    <strong>Ocupacion</strong>
                                    <span><?php echo $ocupacion; ?>%</span>
                                </div>
                                <progress class="class-occupancy-bar occupancy-<?php echo $estado_ocupacion; ?>" max="100" value="<?php echo $ocupacion; ?>">
                                    <?php echo $ocupacion; ?>%
                                </progress>
                                <p class="class-occupancy-copy"><?php echo htmlspecialchars($texto_plazas); ?></p>
                            </div>

                            <?php if ($apuntado): ?>
<!-- formulario principal -->
                                <form action="../app/controllers/gestionar_clase.php" method="POST" class="inline-actions">
                                    <input type="hidden" name="id_clase" value="<?php echo $id_clase; ?>">
                                    <input type="hidden" name="accion" value="desapuntar">
                                    <input type="hidden" name="origen" value="clases.php">
                                    <button type="submit" class="btn btn-secondary">Desapuntarme</button>
                                </form>
                            <?php elseif (!$llena): ?>
<!-- formulario principal -->
                                <form action="../app/controllers/gestionar_clase.php" method="POST" class="inline-actions">
                                    <input type="hidden" name="id_clase" value="<?php echo $id_clase; ?>">
                                    <input type="hidden" name="accion" value="apuntar">
                                    <input type="hidden" name="origen" value="clases.php">
                                    <button type="submit" class="btn btn-primary">Apuntarme</button>
                                </form>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>
</html>

