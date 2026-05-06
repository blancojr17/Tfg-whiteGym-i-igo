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
    }

    $stmt->close();
} else {
    $error_clases = true;
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET["ok"]) && $_GET["ok"] === "creada") {
    $mensaje_exito = "Clase creada correctamente.";
}

if (isset($_GET["error"])) {
    $tipo_error = $_GET["error"];

    if ($tipo_error === "nombre") {
        $mensaje_error = "El nombre es obligatorio.";
    } elseif ($tipo_error === "descripcion") {
        $mensaje_error = "La descripción es obligatoria.";
    } elseif ($tipo_error === "fecha") {
        $mensaje_error = "La fecha es obligatoria.";
    } elseif ($tipo_error === "capacidad") {
        $mensaje_error = "La capacidad debe ser mayor que 0.";
    } else {
        $mensaje_error = "No se pudo crear la clase.";
    }
}

// Base preparada para futuras ampliaciones: edición, cancelación, límites, calendario y estadísticas.
$panel_clases_futuro = [
    "puede_crear" => true,
    "puede_editar" => false,
    "puede_cancelar" => false,
    "limite_reservas" => null,
    "modo_calendario" => false,
    "modo_estadisticas" => false
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área Entrenador</title>
</head>
<body>

<h1>Área de Entrenador</h1>
<p>Bienvenido <?php echo htmlspecialchars($_SESSION["nombre"]); ?></p>

<h2>Crear nueva clase</h2>

<?php if ($mensaje_exito !== ""): ?>
    <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
<?php endif; ?>

<?php if ($mensaje_error !== ""): ?>
    <p><?php echo htmlspecialchars($mensaje_error); ?></p>
<?php endif; ?>

<form action="../app/controllers/crear_clase.php" method="POST">
    <p>
        <label for="nombre">Nombre</label><br>
        <input type="text" id="nombre" name="nombre" required>
    </p>

    <p>
        <label for="descripcion">Descripción</label><br>
        <textarea id="descripcion" name="descripcion" rows="3" required></textarea>
    </p>

    <p>
        <label for="fecha">Fecha</label><br>
        <input type="datetime-local" id="fecha" name="fecha" required>
    </p>

    <p>
        <label for="capacidad">Capacidad</label><br>
        <input type="number" id="capacidad" name="capacidad" min="1" required>
    </p>

    <button type="submit">Crear clase</button>
</form>

<h2>Mis clases asignadas</h2>

<?php if ($error_clases): ?>
    <p>No se han podido cargar tus clases.</p>
<?php elseif (empty($mis_clases)): ?>
    <p>No tienes clases asignadas actualmente.</p>
<?php else: ?>
    <?php foreach ($mis_clases as $clase): ?>
        <article>
            <h3><?php echo htmlspecialchars($clase["nombre"] ?? "Sin nombre"); ?></h3>
            <p><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></p>
            <p><strong>Fecha:</strong> <?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></p>
            <p><strong>Capacidad:</strong> <?php echo (int) ($clase["capacidad"] ?? 0); ?></p>
            <p><strong>Usuarios apuntados:</strong> <?php echo (int) ($clase["total_asistentes"] ?? 0); ?></p>
        </article>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

<a href="../app/controllers/logout.php">Cerrar sesión</a>

</body>
</html>
