<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$plan_activo = null;

$sql = "SELECT up.id_usuario_plan, up.id_plan, p.nombre, p.tipo, up.fecha_fin, up.usos_restantes
        FROM usuarios_planes up
        INNER JOIN planes p ON up.id_plan = p.id_plan
        WHERE up.id_usuario = ?
          AND up.fecha_fin >= CURDATE()
          AND (
                p.tipo = 'suscripcion'
                OR (p.tipo = 'bono' AND up.usos_restantes > 0)
              )
        ORDER BY up.fecha_inicio DESC, up.id_usuario_plan DESC
        LIMIT 1";

$stmt = $conexion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $plan_activo = $resultado ? $resultado->fetch_assoc() : null;
    $stmt->close();
}

$mensaje_exito = "";
$mensaje_error = "";

if (isset($_GET["asistencia_ok"]) && $_GET["asistencia_ok"] === "1") {
    $mensaje_exito = "Asistencia registrada correctamente.";
}

if (isset($_GET["asistencia_error"])) {
    $tipo_error = $_GET["asistencia_error"];

    if ($tipo_error === "sin_plan") {
        $mensaje_error = "No tienes ningún plan activo para registrar entrada.";
    } elseif ($tipo_error === "duplicada") {
        $mensaje_error = "Ya has registrado tu asistencia hoy.";
    } else {
        $mensaje_error = "No se pudo registrar la asistencia. Inténtalo de nuevo.";
    }
}

// Base preparada para futuras ampliaciones: racha, historial y calendario.
$asistencia_resumen = [
    "racha_actual" => null,
    "total_visitas" => null,
    "ultimas_visitas" => []
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Área Cliente - WhiteGym</title>
    <link rel="stylesheet" href="assets/css/cliente.css">
</head>
<body>

<header id="cabecera-cliente">
    <h1>WhiteGym</h1>
    <div id="info-cliente">
        <span><?php echo $_SESSION["nombre"]; ?></span>
        <a href="../app/controllers/logout.php" id="btn-cerrar-sesion">Cerrar sesión</a>
    </div>
</header>

<main id="contenido-cliente">
    <h2>Bienvenido a tu área personal</h2>

    <section>
        <h3>Tu plan activo</h3>

        <?php if ($plan_activo): ?>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($plan_activo["nombre"]); ?></p>
            <p><strong>Tipo:</strong> <?php echo htmlspecialchars($plan_activo["tipo"]); ?></p>
            <p><strong>Fecha fin:</strong> <?php echo htmlspecialchars($plan_activo["fecha_fin"]); ?></p>

            <?php if ($plan_activo["tipo"] === "bono"): ?>
                <p><strong>Usos restantes:</strong> <?php echo (int) $plan_activo["usos_restantes"]; ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p>No tienes ningún plan activo</p>
        <?php endif; ?>
    </section>

    <section>
        <h3>Asistencia</h3>

        <?php if ($mensaje_exito !== ""): ?>
            <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
        <?php endif; ?>

        <?php if ($mensaje_error !== ""): ?>
            <p><?php echo htmlspecialchars($mensaje_error); ?></p>
        <?php endif; ?>

        <form action="../app/controllers/registrar_asistencia.php" method="POST">
            <button type="submit">Registrar entrada</button>
        </form>
    </section>

    <p><a href="planes.php">Ver planes disponibles</a></p>
    <p><a href="clases.php">Ver clases disponibles</a></p>
</main>

</body>
</html>
