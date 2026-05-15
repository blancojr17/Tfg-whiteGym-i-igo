<?php
// panel principal del entrenador
// inicio de sesion
session_start();
// carga de archivos necesarios
require_once __DIR__ . "/../config/conexion.php";

// proteccion de acceso segun rol
if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "entrenador") {
// redireccion final
    header("Location: login.php");
    exit;
}

$id_entrenador = (int) $_SESSION["id_usuario"];
$mis_clases = [];
// mensajes segun el resultado
$error_clases = false;
$total_asistentes = 0;

// consulta sql
$sql = "SELECT c.id_clase,
               c.nombre,
               c.descripcion,
               c.fecha,
               c.capacidad,
               COUNT(uc.id_usuario_clase) AS total_asistentes
        FROM clases c
        LEFT JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
        WHERE c.id_entrenador = ?
        GROUP BY c.id_clase, c.nombre, c.descripcion, c.fecha, c.capacidad
        ORDER BY c.fecha ASC";

// preparacion de la consulta
$stmt = $conexion->prepare($sql);

// comprobacion de la consulta
if ($stmt) {
    $stmt->bind_param("i", $id_entrenador);
// ejecucion de la consulta
    $stmt->execute();
    $resultado = $stmt->get_result();

// lectura de resultados
    while ($fila = $resultado->fetch_assoc()) {
        $mis_clases[] = $fila;
        $total_asistentes += (int) ($fila["total_asistentes"] ?? 0);
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
    if ($_GET["ok"] === "creada") {
// mensajes segun el resultado
        $mensaje_exito = "Clase creada correctamente.";
// recogida de parametros de la url
    } elseif ($_GET["ok"] === "actualizada") {
// mensajes segun el resultado
        $mensaje_exito = "Clase actualizada correctamente.";
// recogida de parametros de la url
    } elseif ($_GET["ok"] === "eliminada") {
// mensajes segun el resultado
        $mensaje_exito = "Clase eliminada correctamente.";
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
    <title>Panel entrenador - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/entrenador.css">
</head>
<body>

<?php include __DIR__ . "/includes/topbar.php"; ?>

<!-- estructura principal del panel -->
<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_entrenador.php"; ?>

<!-- contenido principal -->
    <main class="dashboard-main">
        <div class="page-shell">
<!-- cabecera del contenido -->
            <div class="page-header">
                <div>
                    <span class="eyebrow">Panel entrenador</span>
                    <h2>Gestion de clases</h2>
                    <p>Organiza tus sesiones, revisa el volumen de asistentes y mantÃ©n tus clases ordenadas desde un unico panel.</p>
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
                    <span class="eyebrow">Clases</span>
                    <strong class="metric-value"><?php echo count($mis_clases); ?></strong>
                    <span class="metric-caption">Sesiones asignadas actualmente.</span>
                </article>
<!-- tarjeta de estadisticas -->
                <article class="card card-kpi">
                    <span class="eyebrow">Asistentes</span>
                    <strong class="metric-value"><?php echo $total_asistentes; ?></strong>
                    <span class="metric-caption">Reservas acumuladas en tus clases.</span>
                </article>
            </section>

<!-- bloques de resumen -->
            <section class="split-grid trainer-split">
<!-- tarjeta de contenido -->
                <article class="card" id="crear-clase">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <span class="eyebrow">Gestion</span>
                            <h3>Crear nueva clase</h3>
                            <p>Define los datos basicos de una nueva sesion y dejala lista para reservas.</p>
                        </div>
                    </div>

<!-- formulario principal -->
                    <form action="../app/controllers/crear_clase.php" method="POST" class="form-grid two-columns">
                        <div class="field">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
                        </div>

                        <div class="field">
                            <label for="capacidad">Capacidad</label>
                            <input type="number" id="capacidad" name="capacidad" min="1" placeholder="Capacidad" required>
                        </div>

                        <div class="field">
                            <label for="descripcion">Descripcion</label>
                            <input type="text" id="descripcion" name="descripcion" placeholder="Descripcion" required>
                        </div>

                        <div class="field">
                            <label for="fecha">Fecha y hora</label>
                            <input type="datetime-local" id="fecha" name="fecha" required>
                        </div>

                        <div class="trainer-actions">
                            <button type="submit" class="btn btn-primary">Crear clase</button>
                        </div>
                    </form>
                </article>

<!-- tarjeta de contenido -->
                <article class="card trainer-summary-card">
<!-- cabecera del bloque -->
                    <div class="panel-header">
                        <div>
                            <span class="eyebrow">Control</span>
                            <h3>Resumen rapido</h3>
                            <p>Accede a tus zonas de trabajo principales con una vista mas ordenada.</p>
                        </div>
                    </div>

                    <div class="stack trainer-summary-list">
                        <div class="trainer-summary-item">
                            <strong>Crear clase</strong>
                            <p>Alta de nuevas sesiones con fecha, descripcion y capacidad.</p>
                        </div>
                        <div class="trainer-summary-item">
                            <strong>Tus clases</strong>
                            <p>Consulta asistentes, actualiza datos o elimina sesiones si lo necesitas.</p>
                        </div>
                        <div class="trainer-summary-item">
                            <strong>Gestion diaria</strong>
                            <p>Todo tu trabajo queda dividido en bloques claros y mas faciles de revisar.</p>
                        </div>
                    </div>
                </article>
            </section>

<!-- seccion de clases -->
            <section class="card" id="mis-clases">
<!-- cabecera del bloque -->
                <div class="panel-header">
                    <div>
                        <span class="eyebrow">Tus clases</span>
                        <h3>Clases asignadas</h3>
                        <p>Revisa rapidamente la informacion principal de cada clase y sus asistentes.</p>
                    </div>
                </div>

                <?php if ($error_clases): ?>
                    <p class="notice-error">No se han podido cargar tus clases.</p>
                <?php elseif (empty($mis_clases)): ?>
                    <div class="empty-state">No tienes clases asignadas actualmente.</div>
                <?php else: ?>
<!-- contenedor de la tabla -->
                    <div class="table-wrap">
<!-- tabla de datos -->
                        <table>
<!-- cabecera de la tabla -->
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripcion</th>
                                    <th>Fecha</th>
                                    <th>Capacidad</th>
                                    <th>Asistentes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
<!-- contenido de la tabla -->
                            <tbody>
                                <?php foreach ($mis_clases as $clase): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></td>
                                        <td><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></td>
                                        <td><?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></td>
                                        <td><?php echo (int) ($clase["capacidad"] ?? 0); ?></td>
                                        <td><?php echo (int) ($clase["total_asistentes"] ?? 0); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="editar_clase.php?id_clase=<?php echo (int) ($clase["id_clase"] ?? 0); ?>" class="btn btn-secondary">Editar</a>
<!-- formulario principal -->
                                                <form action="../app/controllers/eliminar_clase.php" method="POST" class="inline-actions">
                                                    <input type="hidden" name="id_clase" value="<?php echo (int) ($clase["id_clase"] ?? 0); ?>">
                                                    <button type="submit">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>

</body>
</html>

