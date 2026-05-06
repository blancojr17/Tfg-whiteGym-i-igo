<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "entrenador") {
    header("Location: login.php");
    exit;
}

$id_entrenador = (int) $_SESSION["id_usuario"];
$id_clase = (int) ($_GET["id_clase"] ?? 0);

if ($id_clase <= 0) {
    header("Location: entrenador.php?error=clase");
    exit;
}

$sql = "SELECT id_clase, nombre, descripcion, fecha, capacidad
        FROM clases
        WHERE id_clase = ? AND id_entrenador = ?
        LIMIT 1";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    header("Location: entrenador.php?error=1");
    exit;
}

$stmt->bind_param("ii", $id_clase, $id_entrenador);
$stmt->execute();
$resultado = $stmt->get_result();
$clase = $resultado ? $resultado->fetch_assoc() : null;
$stmt->close();

if (!$clase) {
    header("Location: entrenador.php?error=permiso");
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
    <title>Editar Clase - WhiteGym</title>
</head>
<body>

<h1>Editar clase</h1>

<form action="../app/controllers/actualizar_clase.php" method="POST">
    <input type="hidden" name="id_clase" value="<?php echo (int) $clase["id_clase"]; ?>">

    <p>
        <label for="nombre">Nombre</label><br>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($clase["nombre"]); ?>" required>
    </p>

    <p>
        <label for="descripcion">Descripción</label><br>
        <textarea id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($clase["descripcion"]); ?></textarea>
    </p>

    <p>
        <label for="fecha">Fecha</label><br>
        <input type="datetime-local" id="fecha" name="fecha" value="<?php echo htmlspecialchars($fecha_form); ?>" required>
    </p>

    <p>
        <label for="capacidad">Capacidad</label><br>
        <input type="number" id="capacidad" name="capacidad" min="1" value="<?php echo (int) $clase["capacidad"]; ?>" required>
    </p>

    <button type="submit">Guardar cambios</button>
</form>

<p><a href="entrenador.php">Volver</a></p>

</body>
</html>
