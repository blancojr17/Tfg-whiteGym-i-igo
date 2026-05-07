<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "admin") {
    header("Location: login.php");
    exit;
}

$id_clase = (int) ($_GET["id_clase"] ?? 0);

if ($id_clase <= 0) {
    header("Location: admin_clases.php?error=admin_clase_id");
    exit;
}

$sql = "SELECT id_clase, nombre, descripcion, fecha, capacidad
        FROM clases
        WHERE id_clase = ?
        LIMIT 1";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: admin_clases.php?error=1");
    exit;
}

$stmt->bind_param("i", $id_clase);
$stmt->execute();
$resultado = $stmt->get_result();
$clase = $resultado ? $resultado->fetch_assoc() : null;
$stmt->close();

if (!$clase) {
    header("Location: admin_clases.php?error=admin_clase_no_existe");
    exit;
}

$fecha_form = "";
if (!empty($clase["fecha"])) {
    $fecha_form = date("Y-m-d\\TH:i", strtotime($clase["fecha"]));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin editar clase - WhiteGym</title>
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
        <div class="page-header">
            <h2>Editar clase (admin)</h2>
        </div>

        <section class="card">
            <form action="../app/controllers/admin_actualizar_clase.php" method="POST" class="inline">
                <input type="hidden" name="id_clase" value="<?php echo (int) $clase["id_clase"]; ?>">
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($clase["nombre"]); ?>" required>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?>">
                <input type="datetime-local" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha_form); ?>" required>
                <input type="number" id="capacidad" name="capacidad" min="1" value="<?php echo (int) $clase["capacidad"]; ?>" required>
                <button type="submit">Guardar cambios</button>
                <a href="admin_clases.php">Volver</a>
            </form>
        </section>
    </main>
</div>

</body>
</html>

