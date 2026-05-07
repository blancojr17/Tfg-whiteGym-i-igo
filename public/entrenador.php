<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "entrenador") {
    header("Location: login.php");
    exit;
}

$id_entrenador = (int) $_SESSION["id_usuario"];
$mis_clases = [];
$error_clases = false;
$total_asistentes = 0;

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

$stmt = $conexion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id_entrenador);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $mis_clases[] = $fila;
        $total_asistentes += (int) ($fila["total_asistentes"] ?? 0);
    }

    $stmt->close();
} else {
    $error_clases = true;
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET["ok"])) {
    if ($_GET["ok"] === "creada") {
        $mensaje_exito = "Clase creada correctamente.";
    } elseif ($_GET["ok"] === "actualizada") {
        $mensaje_exito = "Clase actualizada correctamente.";
    } elseif ($_GET["ok"] === "eliminada") {
        $mensaje_exito = "Clase eliminada correctamente.";
    }
}

if (isset($_GET["error"])) {
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

<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_entrenador.php"; ?>

    <main class="dashboard-main">
        <div class="page-shell">
            <div class="page-header">
                <div>
                    <h2>Clases</h2>
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
                    <span class="eyebrow">Clases</span>
                    <strong class="metric-value"><?php echo count($mis_clases); ?></strong>
                </article>
                <article class="card card-kpi">
                    <span class="eyebrow">Asistentes</span>
                    <strong class="metric-value"><?php echo $total_asistentes; ?></strong>
                </article>
            </section>

            <section class="card" id="crear-clase">
                <div class="panel-header">
                    <div>
                        <h3>Crear clase</h3>
                    </div>
                </div>

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
            </section>

            <section class="card" id="mis-clases">
                <div class="panel-header">
                    <div>
                        <h3>Mis clases</h3>
                    </div>
                </div>

                <?php if ($error_clases): ?>
                    <p class="notice-error">No se han podido cargar tus clases.</p>
                <?php elseif (empty($mis_clases)): ?>
                    <div class="empty-state">No tienes clases asignadas actualmente.</div>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
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
