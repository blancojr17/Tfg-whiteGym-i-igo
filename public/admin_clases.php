<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$clases_globales = [];
$stmt = $conexion->prepare("SELECT c.id_clase, c.nombre, c.descripcion, c.fecha, c.capacidad,
    CONCAT(COALESCE(u.nombre, ''), ' ', COALESCE(u.apellidos, '')) AS entrenador_nombre,
    COUNT(uc.id_usuario_clase) AS total_apuntados
    FROM clases c
    LEFT JOIN usuarios u ON c.id_entrenador = u.id_usuario
    LEFT JOIN usuarios_clases uc ON c.id_clase = uc.id_clase
    GROUP BY c.id_clase, c.nombre, c.descripcion, c.fecha, c.capacidad, u.nombre, u.apellidos
    ORDER BY c.fecha ASC");
if ($stmt) {
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $clases_globales[] = $fila;
    }
    $stmt->close();
}

$mensaje_exito = "";
if (isset($_GET["ok"])) {
    if ($_GET["ok"] === "admin_clase_actualizada") {
        $mensaje_exito = "Clase actualizada correctamente.";
    }
    if ($_GET["ok"] === "admin_clase_eliminada") {
        $mensaje_exito = "Clase eliminada correctamente.";
    }
}
$mensaje_error = isset($_GET["error"]) ? "No se pudo completar la operacion." : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin clases - WhiteGym</title>
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
                    <h2>Clases</h2>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <section class="card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripcion</th>
                                <th>Fecha</th>
                                <th>Capacidad</th>
                                <th>Entrenador</th>
                                <th>Apuntados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clases_globales as $clase): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($clase["nombre"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></td>
                                    <td><?php echo (int) ($clase["capacidad"] ?? 0); ?></td>
                                    <td><?php echo htmlspecialchars(trim((string) ($clase["entrenador_nombre"] ?? "")) !== "" ? $clase["entrenador_nombre"] : "Sin asignar"); ?></td>
                                    <td><?php echo (int) ($clase["total_apuntados"] ?? 0); ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a href="admin_editar_clase.php?id_clase=<?php echo (int) ($clase["id_clase"] ?? 0); ?>" class="btn btn-secondary">Editar</a>
                                            <form action="../app/controllers/admin_eliminar_clase.php" method="POST" class="inline-actions">
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
            </section>
        </div>
    </main>
</div>

</body>
</html>
