<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$mis_clases = [];
$error_clases = false;

$sql = "SELECT c.id_clase,
               c.nombre,
               c.descripcion,
               c.fecha,
               c.capacidad,
               CONCAT(COALESCE(e.nombre, ''), ' ', COALESCE(e.apellidos, '')) AS entrenador
        FROM usuarios_clases uc
        INNER JOIN clases c ON uc.id_clase = c.id_clase
        LEFT JOIN usuarios e ON c.id_entrenador = e.id_usuario
        WHERE uc.id_usuario = ?
        ORDER BY c.fecha ASC";

$stmt = $conexion->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $mis_clases[] = $fila;
    }

    $stmt->close();
} else {
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

<div class="dashboard-layout">
    <?php include __DIR__ . "/includes/sidebar_cliente.php"; ?>

    <main class="dashboard-main">
        <div class="page-shell">
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
                <section class="grid-cards">
                    <?php foreach ($mis_clases as $clase): ?>
                        <?php $entrenador = trim((string) ($clase["entrenador"] ?? "")); ?>
                        <article class="card">
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
                                <p><strong>Capacidad:</strong> <?php echo (int) ($clase["capacidad"] ?? 0); ?> plazas</p>
                            </div>

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
