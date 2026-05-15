<?php
// clases reservadas del usuario
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
$mis_clases = [];
// mensajes segun el resultado
$error_clases = false;

// consulta sql
$sql = "SELECT c.id_clase,
               c.nombre,
               c.descripcion,
               c.fecha,
               c.capacidad,
               CONCAT(COALESCE(e.nombre, ''), ' ', COALESCE(e.apellidos, '')) AS entrenador,
               COUNT(uc_total.id_usuario_clase) AS plazas_ocupadas
        FROM usuarios_clases uc
        INNER JOIN clases c ON uc.id_clase = c.id_clase
        LEFT JOIN usuarios e ON c.id_entrenador = e.id_usuario
        LEFT JOIN usuarios_clases uc_total ON c.id_clase = uc_total.id_clase
        WHERE uc.id_usuario = ?
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
        $mis_clases[] = $fila;
    }

    $stmt->close();
} else {
// mensajes segun el resultado
    $error_clases = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis clases - WhiteGym</title>
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
                    <span class="eyebrow">Mis reservas</span>
                    <h2>Mis clases</h2>
                    <p>Consulta las clases a las que ya estas apuntado y cancela tu plaza si lo necesitas.</p>
                </div>
                <div class="page-actions">
                    <a href="clases.php" class="btn btn-primary">Reservar otra clase</a>
                </div>
            </div>

            <?php if ($error_clases): ?>
                <p class="notice-error">No se han podido cargar tus clases reservadas.</p>
            <?php elseif (empty($mis_clases)): ?>
                <div class="empty-state">Todavia no tienes clases reservadas.</div>
            <?php else: ?>
<!-- bloque principal de contenido -->
                <section class="grid-cards">
                    <?php foreach ($mis_clases as $clase): ?>
                        <?php
                        $entrenador = trim((string) ($clase["entrenador"] ?? ""));
                        $capacidad = (int) ($clase["capacidad"] ?? 0);
                        $ocupadas = (int) ($clase["plazas_ocupadas"] ?? 0);
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
                                    <h3><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></h3>
                                    <p><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></p>
                                </div>
                                <span class="status-pill status-ok">Reservada</span>
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

<!-- formulario principal -->
                            <form action="../app/controllers/gestionar_clase.php" method="POST" class="inline-actions">
                                <input type="hidden" name="id_clase" value="<?php echo (int) ($clase["id_clase"] ?? 0); ?>">
                                <input type="hidden" name="accion" value="desapuntar">
                                <input type="hidden" name="origen" value="mis_clases.php">
                                <button type="submit" class="btn btn-secondary">Cancelar reserva</button>
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

