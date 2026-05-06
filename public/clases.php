<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$clases = [];
$error_clases = false;

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

$stmt = $conexion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {
        $clases[] = $fila;
    }

    $stmt->close();
} else {
    $error_clases = true;
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET["ok"])) {
    if ($_GET["ok"] === "apuntado") {
        $mensaje_exito = "Te has apuntado correctamente a la clase.";
    } elseif ($_GET["ok"] === "desapuntado") {
        $mensaje_exito = "Te has desapuntado correctamente de la clase.";
    }
}

if (isset($_GET["error"])) {
    if ($_GET["error"] === "duplicado") {
        $mensaje_error = "Ya estás apuntado a esa clase.";
    } elseif ($_GET["error"] === "llena") {
        $mensaje_error = "La clase está llena.";
    } elseif ($_GET["error"] === "no_apuntado") {
        $mensaje_error = "No estás apuntado a esa clase.";
    } elseif ($_GET["error"] === "clase") {
        $mensaje_error = "La clase no existe.";
    } else {
        $mensaje_error = "No se pudo completar la operación.";
    }
}

// Base preparada para futuras ampliaciones: creación, edición, cancelación y calendario.
$clases_config_futuro = [
    "permite_crear" => false,
    "permite_editar" => false,
    "permite_cancelar" => false,
    "modo_calendario" => false
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clases - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/cliente.css">
</head>
<body>

<header id="cabecera-cliente">
    <h1>WhiteGym</h1>
    <div id="info-cliente">
        <span><?php echo htmlspecialchars($_SESSION["nombre"]); ?></span>
        <a href="cliente.php">Volver</a>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-cliente">
    <h2>Clases disponibles</h2>

    <?php if ($mensaje_exito !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>

    <?php if ($mensaje_error !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje_error); ?></p>
    <?php endif; ?>

    <?php if ($error_clases): ?>
        <p>No se han podido cargar las clases.</p>
    <?php elseif (empty($clases)): ?>
        <p>No hay clases disponibles.</p>
    <?php else: ?>
        <?php foreach ($clases as $clase): ?>
            <?php
                $id_clase = (int) ($clase["id_clase"] ?? 0);
                $capacidad = (int) ($clase["capacidad"] ?? 0);
                $ocupadas = (int) ($clase["plazas_ocupadas"] ?? 0);
                $apuntado = ((int) ($clase["usuario_apuntado"] ?? 0)) === 1;
                $llena = $ocupadas >= $capacidad;
                $entrenador = trim((string) ($clase["entrenador"] ?? ""));
            ?>
            <article>
                <h3><?php echo htmlspecialchars($clase["nombre"] ?? "Sin nombre"); ?></h3>
                <p><?php echo htmlspecialchars($clase["descripcion"] ?? ""); ?></p>
                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($clase["fecha"] ?? ""); ?></p>
                <p><strong>Capacidad:</strong> <?php echo $capacidad; ?></p>
                <p><strong>Plazas ocupadas:</strong> <?php echo $ocupadas; ?></p>
                <p><strong>Entrenador:</strong> <?php echo htmlspecialchars($entrenador !== "" ? $entrenador : "Sin asignar"); ?></p>

                <?php if ($apuntado): ?>
                    <p><strong>Estado:</strong> Apuntado</p>
                    <form action="../app/controllers/gestionar_clase.php" method="POST">
                        <input type="hidden" name="id_clase" value="<?php echo $id_clase; ?>">
                        <input type="hidden" name="accion" value="desapuntar">
                        <button type="submit">Desapuntarme</button>
                    </form>
                <?php else: ?>
                    <?php if ($llena): ?>
                        <p><strong>Estado:</strong> Clase llena</p>
                    <?php else: ?>
                        <p><strong>Estado:</strong> Plazas disponibles</p>
                        <form action="../app/controllers/gestionar_clase.php" method="POST">
                            <input type="hidden" name="id_clase" value="<?php echo $id_clase; ?>">
                            <input type="hidden" name="accion" value="apuntar">
                            <button type="submit">Apuntarme</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
