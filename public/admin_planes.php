<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$planes = [];
$resColumna = $conexion->query("SHOW COLUMNS FROM planes LIKE 'activo'");
if (!$resColumna || $resColumna->num_rows === 0) {
    $conexion->query("ALTER TABLE planes ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1");
}

$stmt = $conexion->prepare("SELECT id_plan, nombre, precio, tipo, duracion_dias, usos, activo FROM planes ORDER BY id_plan ASC");
if ($stmt) {
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $planes[] = $fila;
    }
    $stmt->close();
}

$mensaje_exito = "";
if (isset($_GET["ok"])) {
    if ($_GET["ok"] === "creado_plan") {
        $mensaje_exito = "Plan creado correctamente.";
    }
    if ($_GET["ok"] === "actualizado_plan") {
        $mensaje_exito = "Plan actualizado correctamente.";
    }
}
$mensaje_error = isset($_GET["error"]) ? "No se pudo completar la operacion." : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin planes - WhiteGym</title>
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
                    <h2>Planes</h2>
                </div>
            </div>

            <?php if ($mensaje_exito !== ""): ?>
                <p class="notice-ok"><?php echo htmlspecialchars($mensaje_exito); ?></p>
            <?php endif; ?>

            <?php if ($mensaje_error !== ""): ?>
                <p class="notice-error"><?php echo htmlspecialchars($mensaje_error); ?></p>
            <?php endif; ?>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Crear plan</h3>
                    </div>
                </div>

                <form action="../app/controllers/gestionar_plan.php" method="POST" class="admin-table-form">
                    <input type="hidden" name="accion" value="crear">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="number" name="precio" min="0" step="0.01" placeholder="Precio" required>
                    <select name="tipo">
                        <option value="suscripcion">Suscripcion</option>
                        <option value="bono">Bono</option>
                    </select>
                    <input type="number" name="duracion_dias" min="0" step="1" value="0" required>
                    <input type="number" name="usos" min="0" step="1" value="0" required>
                    <button type="submit">Crear</button>
                </form>
            </section>

            <section class="card">
                <div class="panel-header">
                    <div>
                        <h3>Planes existentes</h3>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Tipo</th>
                                <th>Duracion</th>
                                <th>Usos</th>
                                <th>Activo</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($planes as $plan): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($plan["nombre"] ?? ""); ?></td>
                                    <td><?php echo htmlspecialchars((string) ($plan["precio"] ?? "0")); ?> EUR</td>
                                    <td><?php echo htmlspecialchars($plan["tipo"] ?? ""); ?></td>
                                    <td><?php echo (int) ($plan["duracion_dias"] ?? 0); ?> dias</td>
                                    <td><?php echo (int) ($plan["usos"] ?? 0); ?></td>
                                    <td><?php echo ((int) ($plan["activo"] ?? 1) === 1) ? "Si" : "No"; ?></td>
                                    <td>
                                        <form action="../app/controllers/gestionar_plan.php" method="POST" class="admin-table-form">
                                            <input type="hidden" name="accion" value="editar">
                                            <input type="hidden" name="id_plan" value="<?php echo (int) ($plan["id_plan"] ?? 0); ?>">
                                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($plan["nombre"] ?? ""); ?>" required>
                                            <input type="number" name="precio" min="0" step="0.01" value="<?php echo htmlspecialchars((string) ($plan["precio"] ?? "0")); ?>" required>
                                            <select name="tipo">
                                                <option value="suscripcion" <?php echo (($plan["tipo"] ?? "") === "suscripcion") ? "selected" : ""; ?>>Suscripcion</option>
                                                <option value="bono" <?php echo (($plan["tipo"] ?? "") === "bono") ? "selected" : ""; ?>>Bono</option>
                                            </select>
                                            <input type="number" name="duracion_dias" min="0" step="1" value="<?php echo (int) ($plan["duracion_dias"] ?? 0); ?>" required>
                                            <input type="number" name="usos" min="0" step="1" value="<?php echo (int) ($plan["usos"] ?? 0); ?>" required>
                                            <select name="activo">
                                                <option value="1" <?php echo ((int) ($plan["activo"] ?? 1) === 1) ? "selected" : ""; ?>>Si</option>
                                                <option value="0" <?php echo ((int) ($plan["activo"] ?? 1) === 0) ? "selected" : ""; ?>>No</option>
                                            </select>
                                            <button type="submit">Guardar</button>
                                        </form>
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
