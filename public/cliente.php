<?php
session_start();
require_once __DIR__ . "/../config/conexion.php";

if (!isset($_SESSION["id_usuario"]) || !isset($_SESSION["email"]) || $_SESSION["rol"] !== "usuario") {
    header("Location: login.php");
    exit;
}

$id_usuario = (int) $_SESSION["id_usuario"];
$plan_activo = null;

$sql = "SELECT p.nombre, p.tipo, up.fecha_fin, up.usos_restantes
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

    <p><a href="planes.php">Ver planes disponibles</a></p>
</main>

</body>
</html>
